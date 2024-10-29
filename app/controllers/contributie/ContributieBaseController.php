<?php
require_once 'app/models/contributie/ContributieBaseModel.php';
require_once 'app/models/contributie/ContributieCrudModel.php';
require_once 'app/models/contributie/ContributieRekenModel.php';

class ContributieBaseController {
    protected $baseModel;
    protected $crudModel;
    protected $rekenModel;

    public function __construct($db) {
        $this->baseModel = new ContributieBaseModel($db);
        $this->crudModel = new ContributieCrudModel($db);
        $this->rekenModel = new ContributieRekenModel($db);
    }
} 