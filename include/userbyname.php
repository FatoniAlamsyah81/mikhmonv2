<?php
/*
 *  Copyright (C) 2018 Laksamadi Guko.
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// hide all error
error_reporting(0);
  
  $getprofile = $API->comm("/ip/hotspot/user/profile/print");
  $srvlist = $API->comm("/ip/hotspot/print");
  
  if(substr($hotspotuser,0,1) == "*"){
	  $hotspotuser = $hotspotuser;
  }elseif(substr($hotspotuser,0,1) != ""){
	  $getuser = $API->comm("/ip/hotspot/user/print", array(
    "?name"=> "$hotspotuser",
    ));
    $hotspotuser =	$getuser[0]['.id'];
    //if($hotspotuser == ""){echo "<b>Hotspot User not found</b>";}
  }
  
  $getuser = $API->comm("/ip/hotspot/user/print", array(
    "?.id"=> "$hotspotuser",
    ));
	$userdetails =	$getuser[0];
	$uid = $userdetails['.id'];
	$userver = $userdetails['server'];
	$uname = $userdetails['name'];
	$upass = $userdetails['password'];
	$uprofile = $userdetails['profile'];
	$uuptime = $userdetails['uptime'];
	$ueduser = $userdetails['disabled'];
	$utimelimit = $userdetails['limit-uptime'];
	$udatalimit = $userdetails['limit-bytes-out'];
  $ucomment = $userdetails['comment'];
	if($udatalimit == ""){$udatalimit = "";}else{$udatalimit = $udatalimit/1000000000;}
	if($uname == $upass){$usermode = "vc";}else{$usermode = "up";}
  
  if($uname == ""){ echo "<b>User not found redirect to user list...</b>"; echo "<script>window.location='./?hotspot=users'</script>";}
  
  $getprofilebyuser = $API->comm("/ip/hotspot/user/profile/print", array(
    "?name" => "$uprofile"));
  $profiledetalis = $getprofilebyuser[0];
  $ponlogin = $profiledetalis['on-login'];
  $getvalid = explode(",",$ponlogin)[3];
  $getprice = explode(",",$ponlogin)[2];
  
  
  $getsch = $API->comm("/system/scheduler/print", array(
    "?name"=> "$uname",
    ));
  $schdetails = $getsch[0]  ;
	$start = $schdetails['start-date'] ." ". $schdetails['start-time'];
	$end = $schdetails['next-run'];
	//$valy = $schdetails['interval'];
	
	
  if(isset($_POST['name'])){
    $server = ($_POST['server']);
    $name = ($_POST['name']);
    $password = ($_POST['pass']);
    $profile = ($_POST['profile']);
    $disabled = ($_POST['disabled']);
    $timelimit = ($_POST['timelimit']);
    $datalimit = ($_POST['datalimit']);
    $comment = ($_POST['comment']);
    if($timelimit == ""){$timelimit = "0";}else{$timelimit = $timelimit;}
    if($datalimit == ""){$datalimit = "0";}else{$datalimit = $datalimit*1000000000;}
    $API->comm("/ip/hotspot/user/set", array(
	    ".id"=> "$uid",
	    "server" => "$server",
	    "name" => "$name",
	    "password" => "$password",
	    "profile" => "$profile",
	    "disabled" => "$disabled",
	    "limit-uptime" => "$timelimit",
			"limit-bytes-out" => "$datalimit",
      "comment" => "$comment",
	    ));
    echo "<script>window.location='./?hotspot-user=".$uid."'</script>";
  }

?>
<script>
  function PassUser(){
    var x = document.getElementById('passUser');
    if (x.type === 'password') {
    x.type = 'text';
    } else {
    x.type = 'password';
    }}
</script>
<div>
<section class="content bg-trp">
<div class="">
<div class="col-12">
<div class="card">
<div class="card-header">
    <h3 class="card-title pull-left">Edit User</h3>
</div>
<!-- /.card-header -->
<div class="card-body">
<div id="example2_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
<div class="row">
<div class="col-sm-12">

<form autocomplete="new-password" method="post" action="">
<table class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
  <tr>
    <th colspan="5">
<?php if($_SESSION['ubp'] != ""){
    echo "    <a class='btn btn-warning' href='./?user-by-profile=".$_SESSION['ubp']."'><i class='fa fa-close'></i> Close</a>";
}elseif($_SESSION['hua'] != ""){
    $_SESSION['ubn'] = "";
    echo "    <a class='btn btn-warning' href='./?hotspot=active'><i class='fa fa-close'></i> Close</a>";
    $_SESSION['hua'] = "";
}elseif($_SESSION['ubn'] != ""){
    echo "    <a class='btn btn-warning' href='./?hotspot=users'><i class='fa fa-close'></i> Close</a>";
    $_SESSION['ubn'] = "";
}else{
    echo "    <a class='btn btn-warning' href='./?hotspot=users'><i class='fa fa-close'></i> Close</a>";
}
?>
    <button type="submit" name="save" class="btn btn-primary btn-mrg" > <i class="fa fa-save"></i> Save</button>
    <a class="btn btn-danger btn-mrg"  href="./?remove-hotspot-user=<?php echo $uid;?>"> <i class="fa fa-minus-square"></i> Remove</a>
    <a class="btn btn-secondary btn-mrg"  title="Print" href="javascript:window.open('./voucher/print.php?user=<?php echo $usermode."-".$uname;?>&qr=no','_blank','width=310,height=450').print();"> <i class="fa fa-print"></i> Print</a>
    <a class="btn btn-info btn-mrg"  title="Print QR" href="javascript:window.open('./voucher/print.php?user=<?php echo $usermode."-".$uname;?>&qr=yes','_blank','width=310,height=450').print();"> <i class="fa fa-qrcode"></i> QR</a>
    <?php if($utimelimit == "1s"){echo '<a class="btn btn-info btn-mrg"  href="./?reset-hotspot-user='.$uid.'"> <i class="fa fa-retweet"></i> Reset</a>';}?>
    </th>
  </tr>
  <tr>
    <td>Enabled</td>
    <td>
			<select class="form-control" name="disabled" required="1">
				<option value="<?php echo $ueduser;?>"><?php if($ueduser == "true"){echo "No";}else{echo "Yes";}?></option>
				<option value="no">Yes</option>
				<option value="yes">No</option>
			</select>
    </td>
  </tr>
  <tr>
    <td>Server</td>
    <td>
			<select class="form-control" name="server" required="1">
				<option><?php if($userver == ""){echo "all";}else{echo $userver;}?></option>
				<option>all</option>
				<?php $TotalReg = count($srvlist);
				for ($i=0; $i<$TotalReg; $i++){
				  echo "<option>" . $srvlist[$i]['name'] . "</option>";
				  }
				?>
			</select>
		</td>
	</tr>
  <tr>
    <td>Name</td><td><input class="form-control" type="text" autocomplete="off" name="name" value="<?php echo $uname;?>"></td>
  </tr>
  <tr>
    <td>Password</td><td>
	<div class="input-group">
      <input class="form-control" id="passUser" type="password" name="pass" autocomplete="new-password" value="<?php echo  $upass;?>">
          <div class="input-group-append">
              <span class="input-group-text"><input title="Show/Hide Password" type="checkbox" onclick="PassUser()"></span>
          </div>
  </div>
		</td>
  </tr>
  <tr>
    <td>Profile</td><td>
			<select class="form-control" name="profile" required="1">
				<option><?php echo $uprofile;?></option>
				<?php $TotalReg = count($getprofile);
				for ($i=0; $i<$TotalReg; $i++){
				  echo "<option>" . $getprofile[$i]['name'] . "</option>";
				  }
				?>
			</select>
		</td>
	</tr>
  <tr>
    <td>Uptime</td><td><?php if($uuptime == 0){}else{echo $uuptime;}?></td>
  </tr>
  <tr>
    <td>Time Limit</td><td><input class="form-control" type="text" size="4" autocomplete="off" name="timelimit" value="<?php if($utimelimit == "1s"){echo "";}else{ echo $utimelimit;}?>"><?php if($utimelimit == "1s"){echo "Expired";}?></td>
  </tr>
  <tr>
    <td>Data Limit</td><td>
      <div class="input-group">
        <input class="form-control" type="number" min="0" max="9999" name="datalimit" value="<?php echo $udatalimit;?>">
          <div class="input-group-append">
              <span class="input-group-text">GB</span>
          </div>
      </div>
    </td>
  </tr>
  <tr>
    <td>Comment</td><td><input class="form-control" type="text"  autocomplete="off" name="comment" value="<?php echo $ucomment;?>"></td>
  </tr>
  <tr>
    <td>Price</td><td><?php if($getprice == 0){}else{echo $curency." ".number_format($getprice);}?></td>
  </tr>
  <?php if($getvalid != ""){?>
  <tr>
    <td>Validity</td><td><?php echo $getvalid;?></td>
  </tr>
  <tr>
    <td>Start</td><td><?php echo $start;?></td>
  </tr>
  <tr>
    <td>End</td><td><?php echo $end;?></td>
  </tr>
  <?php }else{}?>
  <tr>
    <td colspan="2">
      <?php if($curency == "Rp" || $curency == "rp" || $curency == "IDR" || $curency == "idr"){?>
      <p style="padding:0px 5px;">
        Format Time Limit.<br>
        [wdhm] Contoh : 30d = 30hari, 12h = 12jam, 4w3d = 31hari.
      </p>
      <?php }else{?>
      <p style="padding:0px 5px;">
        Format Time Limit.<br>
        [wdhm] Example : 30d = 30days, 12h = 12hours, 4w3d = 31days.
      </p>
      <?php }?>
    </td>
  </tr>
</table>
</form>
</div>
</div>
<!-- /.card-body -->
</div>
<!-- /.card -->
</div>
<!-- /.col -->
</div>
<!-- /.row -->
</section>
</div>	
