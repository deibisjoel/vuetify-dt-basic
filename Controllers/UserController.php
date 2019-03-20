<?php

namespace App\Http\Controllers\API;

use App\User;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
	public function index()
	{
		$search = request('search');
		$query = User::searching($search);
		if ( request('per_page') == -1 ) {
			$perPage  =  $query->count();
	   }else{
			$perPage  = request('per_page');
	   }
		$ordercolumn = request('sortBy');
		$order = request('order');
	   
		return  $query
			->orderBy($ordercolumn ?? 'id', $order ?? 'ASC')
			->paginate($perPage);
	}
    public function update(User $usuario)
    {
        $data = request('data');
        $usuario->update(['name'=> $data['name'],'email' => $data['email']]);
        return ['status'=> true,'message'=>'Usuario actualizado'];
    }

    public function store()
    {
        $data = request('data');
        User::create(['name'=> $data['name'],'email' => $data['email'],'password'=> bcrypt('12356')]);
        return ['status'=> true,'message'=>'Usuario creado'];
    }
    
    public function destroy(User $usuario)
    {
        $usuario->delete();
        return ['status'=> true,'message'=>'Usuario eliminado'];
    }
}
