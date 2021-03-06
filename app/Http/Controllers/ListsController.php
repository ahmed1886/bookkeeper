<?php


namespace Bookkeeper\Http\Controllers;


use Bookkeeper\Http\Controllers\Traits\BasicResource;
use Illuminate\Http\Request;
use Bookkeeper\Exports\PeopleInListExport;
use Maatwebsite\Excel\Facades\Excel;

class ListsController extends BookkeeperController {

    use BasicResource;

    /**
     * Self model path required for ModifiesPermissions
     *
     * @var string
     */
    protected $modelPath = '';
    protected $resourceMultiple = 'lists';
    protected $resourceSingular = 'list';
    protected $resourceName = 'List';
    protected $resourceTitleProperty = 'name';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modelPath = config('models.people_list', \Bookkeeper\CRM\PeopleList::class);
    }

    /**
     * List the specified resource people.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $list = $this->modelPath::findOrFail($id);

        $people = $list->people();

        if(empty($request->input('q')))
        {
            $people = $people->sortable()->paginate();
            $isSearch = false;
        } else {
            $people = $people->search($request->input('q'), null, true)->get();
            $isSearch = true;
        }

        return $this->compileView('lists.show', compact('list', 'people', 'isSearch'), $list->name);
    }

    /**
     * Exports the given resource
     *
     * @param int $id
     * @return download
     */
    public function export($id)
    {
        $export = new PeopleInListExport;
        $export->id = $id;

        return Excel::download($export, 'list-' . date('Y-m-d H:i:s') . '.' . request('format', 'xlsx'));
    }

}
