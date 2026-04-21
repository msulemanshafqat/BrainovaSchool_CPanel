<?php

namespace Modules\BehaviourRecord\Providers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\Academic\ClassSetupRepository;
use App\Repositories\BehaviourRecord\IncidentRepository;
use App\Repositories\Certificate\CertificateRepository;

class BehaviourRecordReportController extends Controller
{

    private $repo;
    private $classSetupRepo;
    private $incidentRepo;

    function __construct(CertificateRepository $repo, ClassSetupRepository $classSetupRepo, IncidentRepository $incidentRepo)
    {
        $this->repo               = $repo;
        $this->classSetupRepo     = $classSetupRepo;
        $this->incidentRepo       = $incidentRepo;
    }

    public function index(){
        $data['title'] = 'Behaviour Record Report';
        $data['certificates']       = $this->repo->all();
        $data['classes']            = $this->classSetupRepo->all();
        $data['incidents']          = $this->incidentRepo->all();

        return view('behaviourrecord::report', compact('data'));
    }
}
