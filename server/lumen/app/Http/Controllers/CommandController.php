<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommandController extends Controller
{
    protected $request;

    /**
     * Constructor.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(string $name)
    {
        return "command list for $name";
    }

    public function next(string $name)
    {
        return "next command for $name";
    }

    public function store(string $name)
    {
        return "adding a command for $name";
    }

    public function show(string $name, int $id)
    {
        return "getting command $id for $name";
    }

    public function replace(string $name, int $id)
    {
        return "replacing command $id for $name";
    }

    public function destroy(string $name, int $id)
    {
        return "deleting command $id for $name";
    }

    public function update(string $name, int $id)
    {
        return "$name is updating command $id";
    }
}
