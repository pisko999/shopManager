<?php
/**
 * Created by PhpStorm.
 * User: spina
 * Date: 17/04/2019
 * Time: 19:29
 */
?>
<table>
    <tr>
        <td>type : </td>
        <td>{{$payment->type}}</td>
    </tr>
    <tr>
        <td>{{$payment->type == "transfer"?"Account : ":"Address :" }}</td>
        <td>{{$payment->type == "transfer"? "2901296514/2010":$payment->address}}</td>
    </tr>
    <tr>
        <td>currency : </td>
        <td>{{$payment->currency}}</td>
    </tr>
</table>
