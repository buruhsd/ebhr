<?php

use Auth;
use App\Models\User;

function CheckApproval(){
    $data = User::first()->level;
}
