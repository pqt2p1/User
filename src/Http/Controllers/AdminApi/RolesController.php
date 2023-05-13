<?php

namespace Pqt2p1\User\Http\Controllers\AdminApi;

use Illuminate\Http\Request;
use Pqt2p1\User\Models\Roles;
use Illuminate\Routing\Controller;
use Pqt2p1\User\Http\Requests\RoleRequest\IndexRoleRequest;
use Pqt2p1\User\Http\Requests\RoleRequest\StoreRoleRequest;
use Pqt2p1\User\Http\Requests\RoleRequest\CreateRoleRequest;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexRoleRequest $request)
    {
        $request->validated();

        // $roles = Role::with('permissions');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateRoleRequest $request)
    {
        $request->validated();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoleRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pqt2p1\User\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function show(Roles $roles)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pqt2p1\User\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function edit(Roles $roles)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pqt2p1\User\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Roles $roles)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pqt2p1\User\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function destroy(Roles $roles)
    {
        //
    }
}
