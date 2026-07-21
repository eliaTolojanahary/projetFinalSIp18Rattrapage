<?php

namespace App\Controllers;

class OperatorLogin extends BaseController
{
    public function form()
    {
        if (session()->get('operator_logged_in')) {
            return redirect()->to('/operator/dahsboard');
        }

        return view('operator/login');
    }

    public function login()
    {
        $login    = trim((string) $this->request->getPost('login'));
        $password = trim((string) $this->request->getPost('password'));

        if ($login === '' || $password === '') {
            return redirect()->back()->withInput()->with('error', 'Login et mot de passe obligatoires.');
        }

        if ($login !== 'admin' || $password !== 'admin') {
            return redirect()->back()->withInput()->with('error', 'Identifiants incorrects.');
        }

        session()->set('operator_logged_in', true);

        return redirect()->to('/operator/dahsboard');
    }

    public function logout()
    {
        session()->remove('operator_logged_in');
        return redirect()->to('/operator/login');
    }
}
