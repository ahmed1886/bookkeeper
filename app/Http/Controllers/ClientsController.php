<?php


namespace Bookkeeper\Http\Controllers;


use Bookkeeper\CRM\Client;
use Bookkeeper\Http\Controllers\Traits\BasicResource;
use Illuminate\Http\Request;

class ClientsController extends BookkeeperController {

    use BasicResource;

    /**
     * Self model path required for ModifiesPermissions
     *
     * @var string
     */
    protected $modelPath = Client::class;
    protected $resourceMultiple = 'clients';
    protected $resourceSingular = 'client';
    protected $resourceName = 'Client';
    protected $resourceTitleProperty = 'name';

    /**
     * List the specified resource jobs.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $jobs = $client->jobs();

        if(empty($request->input('q')))
        {
            $jobs = $jobs->sortable()->paginate();
            $isSearch = false;
        } else {
            $jobs = $jobs->search($request->input('q'), null, true)->get();
            $isSearch = true;
        }

        $people = $client->people()->sortable()->get();

        return $this->compileView('clients.show', compact('client', 'jobs', 'people', 'isSearch'), $client->name);
    }

}