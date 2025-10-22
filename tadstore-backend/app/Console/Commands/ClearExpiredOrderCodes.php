<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderVerification;

class ClearExpiredOrderCodes extends Command
{
    protected $signature = 'orders:clear-expired-codes';
    protected $description = 'Elimina los códigos de verificación de pedido expirados.';

    public function handle()
    {
        $deleted = OrderVerification::where('expires_at', '<', now())->delete();
        $this->info("Códigos eliminados: $deleted");
    }
}
