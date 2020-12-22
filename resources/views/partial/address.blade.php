<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 17/04/2019
 * Time: 18:37
 */
if(!isset($address)){
    $address = new \App\Models\Address([
        'street' => 'Hamry nad Sazavou',
        'number' => 322,
        'city' => 'Zdar nad Sazavou',
        'country' => 'Czechia',
        'postal' => '59101',
    ]);
    $address->name = 'Petr Spinar';
}
?>
<table>
    <tr><td>{{isset($address->name)?$address->name:$user->forename . ' ' . $user->name}}</td></tr>
    <tr><td>{{$address->street}} {{$address->number}}{{ isset($address->flat) && $address->flat != '' ? '/' . $address->flat : ''}}</td></tr>
    <tr><td>{{$address->city}}</td></tr>
    <tr><td>{{$address->postal}} {{$address->country}}</td></tr>
</table>
