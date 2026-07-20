<?php

namespace App\Controllers;

use App\Models\ConfigurationOperatorModel;

class Api extends BaseController
{
    public function prefixes()
    {
        $model = new ConfigurationOperatorModel();
        $prefixes = $model->getPrefixesActifs();

        return $this->response->setJSON($prefixes);
    }
}
