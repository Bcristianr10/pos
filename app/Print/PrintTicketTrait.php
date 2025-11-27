<?php

namespace App\Print;

use App\Models\Setting;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

trait PrintTicketTrait
{
    public function finalyTicket($order)
    {
        $settings = Setting::all();
        try {
            $connector = new WindowsPrintConnector($settings->where('key', 'bussiness_printer')->first()->value);
            $printer   = new Printer($connector);

            // ==========================================
            // ENCABEZADO
            // ==========================================
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("{$settings->where('key', 'bussiness_name')->first()->value}\n");
            $printer->selectPrintMode();
            $printer->text("RUC: {$settings->where('key', 'business_ruc')->first()->value} DV {$settings->where('key', 'business_dv')->first()->value}\n");
            $printer->text("{$settings->where('key', 'business_address')->first()->value}\n");
            $printer->text("Tel: {$settings->where('key', 'business_phone')->first()->value}  |  {$settings->where('key', 'business_email')->first()->value}\n");
            $printer->text("------------------------------------------------\n");

            // ==========================================
            // TÍTULO DOCUMENTO
            // ==========================================
            $printer->setEmphasis(true);
            $printer->text("FACTURA ELECTRÓNICA\n");
            $printer->text("(Documento Tributario)\n");
            $printer->setEmphasis(false);
            $printer->text("------------------------------------------------\n");

            // ==========================================
            // INFO FACTURA
            // ==========================================
            $orderNumber = str_pad($order->id, 12, '0', STR_PAD_LEFT);
            $userName = $order->cashier->name ?? 'General';
            $customerName = $order->customer_name ?? 'Consumidor Final';
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Factura: FE-{$orderNumber}\n");
            $printer->text("Fecha: " . date('d/m/Y') . "    Hora: " . date('H:i') . "\n");
            $printer->text("Caja: 01      Cajero: {$userName}\n");
            $printer->text("Cliente: {$customerName}\n");
            if ($order->customer_ruc) {
                $printer->text("Ruc: {$order->customer_ruc}    Dv: {$order->customer_dv}\n");
            }
            $printer->text("------------------------------------------------\n");

            // ==========================================
            // DETALLE
            // ==========================================
            $printer->setEmphasis(true);
            $printer->text("CANT  DESCRIPCION                          TOTAL\n");
            $printer->setEmphasis(false);
            $printer->text("------------------------------------------------\n");

            $ancho = 48; // tu impresora imprime 78 columnas exactas

            foreach ($order->items as $item) {

                $cant  = $item->quantity;
                $desc  = $item->product->name;
                $total = number_format($item->price, 2);
                // base: CANT + espacio + descripción
                $linea = "{$cant}  {$desc}";

                // cuántos espacios necesitamos para empujar el total a la derecha
                $espacios = $ancho - strlen($linea) - strlen($total);

                if ($espacios < 1) {
                    $espacios = 1; // nunca permitir negativos
                }

                // imprimir línea perfecta
                $printer->text($linea . str_repeat(" ", $espacios) . $total . "\n");
            }



            $printer->text("------------------------------------------------\n");

            // ==========================================
            // TOTALES
            // ==========================================
            $subtotal = number_format($order->items->sum('total'), 2);
            $tax = number_format($order->items->sum('tax'), 2);
            $total = number_format($order->total, 2);
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text("SUBTOTAL:          " . $subtotal . "\n");
            $printer->text("ITBMS:          " . $tax . "\n");
            $printer->text("------------------------------------------------\n");

            $printer->setEmphasis(true);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("TOTAL: " . $total . "\n");
            $printer->selectPrintMode();
            $printer->setEmphasis(false);

            $printer->text("------------------------------------------------\n");

            // ==========================================
            // MÉTODO DE PAGO
            // ==========================================
            $paymentMethodsToString = implode(', ', $order->paymentMethods->pluck('id','name')->toArray());
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Método de Pago: {$paymentMethodsToString}\n");
            $printer->text("Cambio: 0.03\n");
            $printer->text("------------------------------------------------\n");

            // ==========================================
            // CUFE
            // ==========================================
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("CUFE:\n");
            $printer->setEmphasis(false);
            $printer->text("3F5A-7C98-AD23-44B3-91EF-F24693A8BAC1\n");
            $printer->text("\n");

            // ==========================================
            // QR
            // ==========================================
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Escanee para validar su factura\n\n");

            $qrUrl = "https://factura.e.gob.pa/validar?cufe=3F5A7C98AD2344B391EFF24693A8BAC1";
            $printer->qrCode($qrUrl, Printer::QR_ECLEVEL_L, 6);
            $printer->feed(2);

            // ==========================================
            // URL VALIDACIÓN
            // ==========================================
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("URL VALIDACIÓN DGI:\n");
            $printer->text("$qrUrl\n");
            // $printer->text("------------------------------------------------\n");

            // ==========================================
            // PIE LEGAL
            // ==========================================
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            // $printer->setEmphasis(true);
            $printer->text("Documento tributario emitido\n");
            $printer->text("y autorizado por la DGI Panamá\n");
            // $printer->setEmphasis(false);
            $printer->text("------------------------------------------------\n");

            // ==========================================
            // MENSAJE FINAL
            // ==========================================
            $printer->setEmphasis(true);
            $printer->text("¡GRACIAS POR SU COMPRA!\n");
            $printer->setEmphasis(false);
            $printer->text("Conserva esta factura como soporte\n");
            $printer->text("------------------------------------------------\n");


            $printer->feed(3);
            $printer->cut();
            $printer->close();

            return "Factura impresa correctamente.";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
