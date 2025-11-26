<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class TicketController extends Controller
{

    public function index()
    {
        $this->finalyTicket();
        // $this->printFacturaPremium();
        // $this->printFactura();
        // $this->firstPrint();
        return view('ticket.ticket');
    }

    public function printFactura()
    {
        try {
            // Nombre que aparece en Panel de impresoras
            $connector = new WindowsPrintConnector("POS-58");
            $printer = new Printer($connector);

            // --- ENCABEZADO ---
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("CAFÉ PANAMÁ S.A.\n");
            $printer->setEmphasis(false);
            $printer->text("RUC: 1554879-1-789563 DV 45\n");
            $printer->text("Tel: 6789-1234\n");
            $printer->text("Calle 50, Ciudad de Panamá\n");
            $printer->feed();

            // --- DOCUMENTO ---
            $printer->setEmphasis(true);
            $printer->text("FACTURA ELECTRÓNICA\n");
            $printer->setEmphasis(false);
            $printer->text("Fecha: 2025-01-12 14:35\n");
            $printer->text("CUFE: ABCD-1234-EF56-7890\n");
            $printer->text("----------------------------------------\n");

            // --- DETALLE ---
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Cant   Descripción              Total\n");
            $printer->text("----------------------------------------\n");

            $printer->text("1      Café Americano        2.50\n");
            $printer->text("2      Empanada de Pollo     5.00\n");
            $printer->text("1      Jugo Natural          3.00\n");

            $printer->text("----------------------------------------\n");

            // --- TOTALES ---
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text("Subtotal: 10.50\n");
            $printer->text("ITBMS 7%: 0.73\n");
            $printer->setEmphasis(true);
            $printer->text("TOTAL: 11.23\n");
            $printer->setEmphasis(false);

            $printer->feed(2);

            // --- MENSAJE FINAL ---
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Gracias por su compra\n");
            $printer->text("www.cafepanama.com\n");
            $printer->feed(2);

            $printer->cut();
            $printer->close();

            return "Impresión completada";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function firstPrint()
    {
        $connector = new WindowsPrintConnector("POS-58"); // Ajusta nombre
        $printer = new Printer($connector);

        // ENCABEZADO
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->text("MI NEGOCIO S.A.\n");
        $printer->selectPrintMode();
        $printer->text("RUC: 155432-1-123456 DV 45\n");
        $printer->text("Av. Central, Local #12, Ciudad de Panamá\n");
        $printer->text("Tel: 394-0000    Email: info@minegocio.com\n");
        $printer->text("------------------------------------------\n");

        $printer->selectPrintMode(Printer::MODE_EMPHASIZED);
        $printer->text("FACTURA ELECTRÓNICA\n");
        $printer->selectPrintMode();

        // DATOS
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("------------------------------------------\n");
        $printer->text("Factura: FE-00001234\n");
        $printer->text("Fecha: 22/11/2025   Hora: 15:42\n");
        $printer->text("Caja: 01   Cajero: Juan Pérez\n");
        $printer->text("Cliente: Consumidor Final\n");
        $printer->text("------------------------------------------\n");

        // DETALLE
        $printer->text("CANT  DESCRIPCION            TOTAL\n");
        $printer->text("------------------------------------------\n");
        $printer->text("1     Café Americano         2.50\n");
        $printer->text("1     Emparedado de Pollo    4.75\n");
        $printer->text("2     Gaseosa 355ml          3.00\n");

        // TOTALES
        $printer->text("------------------------------------------\n");
        $printer->text("SUBTOTAL:                    10.25\n");
        $printer->text("ITBMS (7%):                   0.72\n");
        $printer->text("------------------------------------------\n");

        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->text("TOTAL:                       10.97\n");
        $printer->selectPrintMode();
        $printer->text("------------------------------------------\n");

        $printer->text("Método de Pago: Efectivo\n");
        $printer->text("Cambio: 0.03\n");
        $printer->text("------------------------------------------\n");

        // CUFE
        $printer->text("CUFE:\n");
        $printer->text("3F5A-7C98-AD23-44B3-91EF-F24693A8BAC1\n\n");

        // QR (ficticio)
        $printer->qrCode(
            "https://factura.e.gob.pa/validar?cufe=3F5A7C98AD2344B391EFF24693A8BAC1",
            Printer::QR_ECLEVEL_L,
            6
        );
        $printer->text("\n");

        // PIE
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("¡GRACIAS POR SU COMPRA!\n");
        $printer->text("Conserva esta factura como soporte\n");
        $printer->text("------------------------------------------\n\n\n");

        $printer->cut();
        $printer->close();
    }

    public function printFacturaPremium()
    {
        try {
            $connector = new WindowsPrintConnector("POS-58");
            $printer = new Printer($connector);

            // ======= ENCABEZADO =======
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("CAFÉ PANAMÁ S.A.\n");
            $printer->selectPrintMode();
            $printer->setEmphasis(false);

            $printer->text("RUC: 1554879-1-789563 DV 45\n");
            $printer->text("Tel: 6789-1234\n");
            $printer->text("Calle 50, Ciudad de Panamá\n");
            $printer->feed();

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("***** FACTURA ELECTRÓNICA *****\n");
            $printer->setEmphasis(false);

            $printer->text("------------------------------\n");

            // ======= DATOS FACTURA =======
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Fecha: 2025-01-12 14:35\n");
            $printer->text("Factura Nº: FE-001256\n");
            $printer->text("CUFE: F3A1E5-99A1-478E-A320\n");
            $printer->text("Método de Pago: Tarjeta\n");
            $printer->text("------------------------------\n");

            // ======= DATOS CLIENTE =======
            $printer->setEmphasis(true);
            $printer->text("CLIENTE\n");
            $printer->setEmphasis(false);

            $printer->text("Juan Pérez\n");
            $printer->text("Cédula: 8-765-2345\n");
            $printer->text("Correo: jperez@test.com\n");
            $printer->text("------------------------------\n");

            // ======= DETALLE =======
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text("Cant  Descripción            Total\n");
            $printer->setEmphasis(false);
            $printer->text("--------------------------------\n");

            $items = [
                ["cant" => 1, "desc" => "Café Americano", "total" => 2.50],
                ["cant" => 2, "desc" => "Empanada Carne", "total" => 6.00],
                ["cant" => 1, "desc" => "Capuchino", "total" => 3.25],
                ["cant" => 1, "desc" => "Galleta Choco", "total" => 1.50],
            ];

            foreach ($items as $it) {
                $line = str_pad($it["cant"], 4) . " " .
                    str_pad(substr($it["desc"], 0, 18), 18) . " " .
                    number_format($it["total"], 2);
                $printer->text($line . "\n");
            }

            $printer->text("--------------------------------\n");

            // ======= TOTALES =======
            $printer->setJustification(Printer::JUSTIFY_RIGHT);

            $printer->text("Subtotal: 13.25\n");
            $printer->text("ITBMS 7%: 0.93\n");

            $printer->setEmphasis(true);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("TOTAL: 14.18\n");
            $printer->selectPrintMode();
            $printer->setEmphasis(false);

            $printer->feed();

            // ======= QR DE FE =======
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Verifique su factura:\n");
            $printer->feed();

            $qrData = "https://dgi-fep.panama.gob.pa/verify?cufe=F3A1E5-99A1-478E-A320";
            $printer->qrCode($qrData, Printer::QR_ECLEVEL_L, 6);

            $printer->feed(2);

            // ======= INFORMACIÓN LEGAL =======
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("Documento Tributario Electrónico\n");
            $printer->setEmphasis(false);

            $printer->text("Autorizado por la DGI Panamá\n");
            $printer->text("Válido sin firma autógrafa\n");
            $printer->feed();

            // ======= PIE =======
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("------------------------------\n");
            $printer->text("Gracias por su compra\n");
            $printer->text("www.cafepanama.com\n");

            $printer->feed(3);
            $printer->cut();
            $printer->close();

            return "Factura Premium impresa";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function finalyTicket()
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

            foreach ($items as $it) {

                $cant  = $it["cant"];
                $desc  = $it["desc"];
                $total = number_format($it["total"], 2);

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
