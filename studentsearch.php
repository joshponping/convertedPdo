<?php
    if(!ini_get('safe_mode'))
    {
        set_time_limit(0);
    }

    include_once('../include/zz.php');
    include_once('../studentmanagement/cufunctions.php');

    if(isset($_POST['submit']))
    {
        if($_POST['tos'] != '')
        {
            if($_POST['tos'] == 'curr')
            {
                //$getthestud = "SELECT * FROM reglist WHERE studentshipStatus <> '5'";
                $getthestud = "SELECT * FROM reglstcurr WHERE studentshipStatus <> '5'";
                $curr = 'yes';
            }else if($_POST['tos'] == 'grad')
            {
                $getthestud = "SELECT * FROM gradlist WHERE 1";
                $grad = 'yes';
            }
        }        
        if($_POST['nm'] != '')
        {
            $search = 'yes';
            $nm = mysql_real_escape_string(trim($_POST['nm']));
            $getthestud .= " AND nm LIKE '%$nm%'";
        }

        if($_POST['matric'] != '')
        {
            $search = 'yes';
            $matno = mysql_real_escape_string(trim($_POST['matric']));
            $getthestud .= " AND matno LIKE '%$matno%'";
        }

        if($_POST['hall']!='')
        {
            $search = 'yes';
            $hall=$_POST['hall'];
            $getthestud .=" AND hall LIKE '%$hall%'";
        }

        if($_POST['gender']!='')
        {   
            $search = 'yes';
            if($_POST['gender'] == 'm')
            {
                $gender='male';
                $sex='m';
                $getthestud .=" AND (sex = '$gender' OR sex = '$sex')";
            }else if($_POST['gender'] == 'f'){
                $gender='female';
                $sex='f';
                $getthestud .=" AND (sex = '$gender' OR sex = '$sex')";
            }
        }        

        if($_POST['program']!='')
        {
            $search = 'yes';
            $program=$_POST['program'];
            $getthestud .=" AND program LIKE '%$program%'";
        }

        if($_POST['level'] != '')
        {
            $search = 'yes';
            $level = $_POST['level'];
            $getthestud .= " AND level LIKE '%$level%'";
        }        

        $getthestud .= " ORDER BY program, nm";

        $gettingthestud = mysql_query($getthestud) or die('error 1');

        $getthesearch = mysql_query($getthestud) or die('error 2');

        if(isset($search))
        {
            if(isset($curr))
            {
                $outputArray = array(0=>array(0=>"NAME",1=>"MATRIC",2=>"PROGRAM",3=>"STUDENTSHIP",4=>"DISCIPLINARY",5=>"CLEARANCE",6=>"LEVEL",7=>"HALL"));
                // Student status should be added
            }
            if(isset($grad))
            {
                $outputArray = array(0=>array(0=>"NAME",1=>"REGNO",2=>"MATRIC",3=>"PROGRAM",4=>"YEAR",5=>"CLASS", 5=>"CGPA"));
            }
            
            $c = 0;

            while($row = mysql_fetch_array($getthesearch))
            {
                $name = ucwords(strtolower($row['nm']));
                $matno = $row['matno'];
                $program = ucwords(strtolower($row['program']));
                if(isset($curr))
                {
                    $studentship = $row['studentshipStatus'];
                    $disciplinary = $row['disciplinaryStatus'];
                    $clearance = $row['clearanceStatus'];
                    list($id,$status) = rectrievestudentshipstatus($studentship);
                    list($id,$disciplinarystatus) = rectrievedisciplinarystatus($disciplinary);
                    list($id,$clearancestatus) = rectrieveclearancestatus($clearance);
                    $level = $row['level'];
                    $hall = $row['hall'];
                }
                if(isset($grad))
                {
                    $fno = $row['FNO'];
                    $YOG = $row['YOG'];
                    $KL = $row['KL'];
                    $cgpa1 = $row['cgpa1'];
                }
                if(isset($curr))
                {
                    $temp = array(++$c=>array(0=>$name,1=>$matno,2=>$program,3=>$status,4=>$disciplinarystatus,5=>$clearancestatus,6=>$level,7=>$hall));
                }
                if(isset($grad))
                {
                    $temp = array(++$c=>array(0=>$name,1=>$fno,2=>$matno,3=>$program,4=>$YOG,5=>$KL,5=>$cgpa1));
                }
                $outputArray = array_merge($outputArray,$temp);
            }

            $_SESSION["myDataArray"] = $outputArray;
            $_SESSION['filename'] = "Students_".date('Y');
        }        
    }

    $gethostel = "SELECT * FROM hostels";
    $gettinghostel = mysql_query($gethostel);

    $serial = 1;
    $inc = 1;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
       

        <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
        <link rel="stylesheet" href="../assets/css/mystudsearch.css" />
        <script type="text/javascript"  src="../assets/js/jquery.js"></script> 
        <script type="text/javascript"  src="../assets/js/bootstrap.min.js"></script> 
        <script type="text/javascript"  src="../assets/js/mystudsearch.js"></script> 

        <script type="text/javascript">
            function printContent(el){
                    var restorepage = document.body.innerHTML;
                    var printcontent = document.getElementById(el).innerHTML;
                    document.body.innerHTML = printcontent;
                    window.print();
                    document.body.innerHTML = restorepage;
            }
        </script>
        <title>STUDENT SEARCH</title>
    </head>
    <body>
        <center>
            <fieldset>
                <legend>SEARCH FOR STUDENT(S)</legend>
                <form action="" method="post" class="tab-form" >
                <table> 
                    <tr>
                        <td>
                            <strong>
                                Type of Student:
                            </strong>
                        </td>
                        <td>
                            <select name="tos" id='tos' required >
                                <option disabled>---- Select ----</option>
                                <option selected value='curr'>Not Graduated Student</option>
                                <option value='grad'>Graduated Student</option>
                            </select>
                        </td>
                    </tr>   
                    <tr>
                        <td>
                            <strong>
                                Name:
                            </strong>
                        </td>
                        <td>
                            <input name="nm" type="text" id='nm' />
                        </td>
                    </tr>                                       
                    <tr>
                        <td>
                            <strong>
                                Matric No.:
                            </strong>
                        </td>
                        <td>
                            <input name="matric" type="text" id='matric' />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>
                                Gender:
                            </strong>
                        </td>
                        <td>
                            <select name="gender" id='gender' >
                                <option value='' selected>---- Select ----</option>
                                <option value='m'>Male</option>
                                <option value='f'>Female</option>
                            </select>
                        </td>
                    </tr>                    
                    <tr id='dhall'>
                        <td>
                            <strong>
                                Hall:
                            </strong>
                        </td>
                        <td>
                            <select name="hall" id="hall" >
                                <option value='' selected>---- Select ----</option>
                                <?php while($hostelrow = mysql_fetch_array($gettinghostel)):?>
                                <option value="<?php echo $hostelrow['id'];?>"><?php echo $hostelrow['hostelName'];?></option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>
                                Program:
                            </strong>
                        </td>
                        <td>
                            <select name="program" id="program" >
                                <?php echo homeprogram(); ?>
                            </select>
                        </td>
                    </tr>                    
                    <tr id='dlevel'>
                        <td>
                            <strong>
                                Level:
                            </strong>
                        </td>
                        <td>
                            <select name="level" id='level'>
                                <option value='' selected>---- Select ----</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                                <option value="300">300</option>
                                <option value="400">400</option>
                                <option value="500">500</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="submit" name="submit" value="Search" />
                        </td>
                    </tr>
                </table>
            </fieldset>
        </center>
        <?php if(isset($gettingthestud)):?>
        <?php if((mysql_num_rows($gettingthestud) > 0) && (isset($search))):?>      
        <p><a href="" onclick="printContent('studentprint')">Print Student(s)</a></p>
        <p><a href="exportXSL.php?x=csv">Download CSV Format</a></p>
        <div id='studentprint'>
            <table border=1>
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>NAME</th>
                        <?php if(isset($grad)): ?>
                        <th>GENDER.</th>
                        <?php endif;?>
                        <th>MATRIC NO.</th>
                        <th>PROGRAM</th>
                        <?php if(isset($curr)): ?>
                        <th>STUDENTSHIP STATUS</th>
                        <th>DISCIPLINARY STATUS</th>
                        <th>CLEARANCE STATUS</th>
                        <th>LEVEL</th>
                        <?php endif;?>
                        <?php if(isset($grad)): ?>
                        <th>YEAR</th>
                        <th>CLASS</th>
                        <th>CGPA</th>
                        <?php endif;?>
                        <th>PICTURE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($studrow = mysql_fetch_array($gettingthestud)): ?>
                    <tr>
                        <td><?php echo $serial; ?></td>
                        <td><?php echo ucwords(strtolower($studrow['nm'])); ?><?php if(isset($curr)):?><br><a href='#' id='viewprofile' data-profile="<?php echo $studrow['userid']; ?>" data-toggle="modal" data-target="#profile">(View Profile)</a><?php endif; ?></td>
                        <?php if(isset($grad)): ?>
                        <td><?php echo $studrow['sex']; ?></td>
                        <?php endif;?>
                        <td><?php echo $studrow['matno']; ?></td>
                        <td><?php echo $studrow['program']; ?></td>
                        <?php if(isset($curr)): ?>
                        <td><?php list($id,$status) = rectrievestudentshipstatus($studrow['studentshipStatus']); echo $status ; ?></td>
                        <td><?php list($id,$disciplinarystatus) = rectrievedisciplinarystatus($studrow['disciplinaryStatus']);echo $disciplinarystatus; ?></td>
                        <td><?php list($id,$clearancestatus) = rectrieveclearancestatus($studrow['clearanceStatus']); echo $clearancestatus; ?></td>
                        <td><?php echo $studrow['level']; ?></td>
                        <?php endif;?>
                        <?php if(isset($grad)): ?>
                        <td><?php echo $studrow['YOG']; ?></td>
                        <td><?php echo $studrow['KL']; ?></td>
                        <td><?php echo number_format($studrow['cgpa1'],2); ?></td>
                        <?php endif;?>
                        <?php 
                            $thematno = $studrow['matno'];
                            $filename = "../Images/passport/".$thematno.".jpg";
                            $filename2 = "../Images/100passport/".$thematno.".jpg"; 
                        ?>
                        <?php if(file_exists($filename)){ ?>
                        <td><img src='<?php echo $filename; ?>' alt='my picture' height='60' width='60' /></td>
                        <?php }else if(file_exists($filename2)){ ?>
                        <td><img src='<?php echo $filename2; ?>' alt='my picture' height='60' width='60' /></td>
                        <?php }else{ ?>
                        <td><img src='../assets/img/profile.jpg' alt='my picture' height='60' width='60' /></td>
                        <?php } ?>
                    </tr>
                    <?php $serial = $serial + $inc; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php if((mysql_num_rows($gettingthestud) > 0) && (!isset($search))):?>
        <p>Kindly filter your search.</p>
        <?php endif; ?>
        <?php if((mysql_num_rows($gettingthestud) < 1) && (isset($search))):?>
        <p>No result found.</p>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Modal Confirmation -->
        <div class="modal fade" id="profile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h1 class="modal-title">
                            <img src='../assets/img/profile.jpg' id='profilepic' alt='my picture' height='50' width='50' />
                            <span id='profilename'>My Profile Name</span>
                        </h1>
                    </div>
                    <div class="modal-body">
                        <div class='row'>
                            <div class='first-column'>
                                <ul class='profiletitle tabs'>
                                    <li class='active'><a data-tab-id = "basic">Basic Info</a></li>
                                    <li><a data-tab-id = "courses">Courses</a></li>
                                    <li><a data-tab-id = "timetable">Timetable</a></li>
                                    <li><a data-tab-id = "finance">Finance</a></li>
                                    <li><a data-tab-id = "ca">Semester C.A.</a></li>
                                    <li><a data-tab-id = "results">Results</a></li>
                                    <li><a data-tab-id = "transcript">Transcript</a></li>
                                    <li><a data-tab-id = "history">History</a></li>
                                </ul>
                            </div>
                            <div class='second-column'>
                                <div id='basic' class="active">
                                  <p>Loading...</p>
                                </div>
                                <div id='courses'>
                                  <p>Loading...</p>
                                </div>
                                <div id='timetable'>
                                  <p>Loading...</p>
                                </div>
                                <div id='finance'>
                                  <p>Loading...</p>
                                </div>
                                <div id='ca'>
                                  <p>Loading...</p>                                    
                                </div>
                                <div id='results'>
                                  <p>Loading...</p>       
                                </div>
                                <div id='transcript'>
                                   <p>Loading...</p>
                                </div>
                                <div id='history'>
                                   <p>Loading...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        <!-- End of Modal Confirmation -->
    </body>
</html>