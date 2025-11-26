<?php

namespace App\Print;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

trait PrintTicketTrait
{
    public function finalyTicket($order)
    {
        try {
            $connector = new WindowsPrintConnector("POS-58");
            $printer   = new Printer($connector);

            // ==========================================
            // ENCABEZADO
            // ==========================================
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("MI NEGOCIO S.A.\n");
            $printer->selectPrintMode();
            $printer->text("RUC: 155432-1-123456 DV 45\n");
            $printer->text("Av. Central, Local #12, Ciudad de Panamá\n");
            $printer->text("Tel: 394-0000  |  info@minegocio.com\n");
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
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Factura: FE-00001234\n");
            $printer->text("Fecha: 22/11/2025    Hora: 15:42\n");
            $printer->text("Caja: 01      Cajero: Juan Pérez\n");
            $printer->text("Cliente: Consumidor Final\n");
            $printer->text("------------------------------------------------\n");

            // ==========================================
            // DETALLE
            // ==========================================
            $printer->setEmphasis(true);
            $printer->text("CANT  DESCRIPCION                          TOTAL\n");
            $printer->setEmphasis(false);
            $printer->text("------------------------------------------------\n");

            $items = [
                ["cant" => 1, "desc" => "Café Americano", "total" => 2.50],
                ["cant" => 1, "desc" => "Emparedado de Pollo", "total" => 4.75],
                ["cant" => 2, "desc" => "Gaseosa 355ml", "total" => 3.00],
            ];

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
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text("SUBTOTAL:          10.25\n");
            $printer->text("ITBMS (7%):          0.72\n");
            $printer->text("------------------------------------------------\n");

            $printer->setEmphasis(true);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("TOTAL: 10.97\n");
            $printer->selectPrintMode();
            $printer->setEmphasis(false);

            $printer->text("------------------------------------------------\n");

            // ==========================================
            // MÉTODO DE PAGO
            // ==========================================
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Método de Pago: Efectivo\n");
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
