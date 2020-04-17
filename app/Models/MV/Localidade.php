<?php

namespace App\Models\MV;

use Illuminate\Database\Eloquent\Model;

class Localidade extends Model
{
    protected $table        = 'localidade';
    protected $connection   = 'oracle';

    public function setores()
    {
        return $this->belongsTo(Setor::class, "cd_setor", "cd_setor");
    }
}
