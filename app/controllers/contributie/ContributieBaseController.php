<?php
/**
 * ContributieBaseController
 * 
 * Basis controller voor contributie functionaliteit
 * Bevat gedeelde functionaliteit voor alle contributie controllers
 */
require_once 'app/models/contributie/ContributieBaseModel.php';
require_once 'app/models/contributie/ContributieCrudModel.php';
require_once 'app/models/contributie/ContributieRekenModel.php';

class ContributieBaseController {
    /** @var ContributieBaseModel Instance van het ContributieBaseModel */
    protected $baseModel;
    
    /** @var ContributieCrudModel Instance van het ContributieCrudModel */
    protected $crudModel;
    
    /** @var ContributieRekenModel Instance van het ContributieRekenModel */
    protected $rekenModel;

    /**
     * Constructor initialiseert alle benodigde models
     * 
     * @param PDO $db Database connectie object
     */
    public function __construct($db) {
        $this->baseModel = new ContributieBaseModel($db);
        $this->crudModel = new ContributieCrudModel($db);
        $this->rekenModel = new ContributieRekenModel($db);
    }
} 