<?php
// Этапы олимпиады
//error_reporting(E_ALL);

include_once('olimp_common.inc');
//var_dump( $_POST );
function delete_rows()
{
    foreach ( $_POST as $key => $value )
    {
        mysql_query('DELETE FROM ehope_work.olimp_stages WHERE olimp_stages.id = '.$key );
    }
}

function update_bd()
{
    $res=mysql_query('UPDATE olimp_stages 
                             SET exam="'.($_POST['n_exam']).'", 
	                             classes = "'.($_POST['n_classes']).'", 
	                             place = "'.($_POST['n_place']).'" 
                             WHERE id =  "'.($_POST['id']).'"
                     ' );
    echo 'UPDATE olimp_stages 
                             SET exam="'.($_POST['n_exam']).'", 
	                             classes = "'.($_POST['n_classes']).'", 
	                             place = "'.($_POST['n_place']).'", 
                             WHERE id =  "'.($_POST['id']).'"
                     ';
}

function get_row_bd( $id )
{
    $res=mysql_query('select e.name as exam, s.id as id,
                                            p.school_class as school_class,
                                            pl.name as place,
                                            s.stage as stage,
                                            f.name as form, 
                                            DATE_FORMAT(s.date_exam,"%d.%m.%Y") as dt,
                                            DATE_FORMAT(s.date_breg,"%d.%m.%Y") as dt_breg,
                                            DATE_FORMAT(s.date_ereg,"%d.%m.%Y") as dt_ereg,
                                            DATE_FORMAT(s.date_post,"%d.%m.%Y") as dt_post,
                                            s.var_prefix as var_prefix,
                                            s.min_var as min_var,
                                            s.max_var as max_var,
                                            s.grp_limit as grp_limit,
                                            s.task_count as task_count,
                                            s.task_weight as task_weight,
                                            s.grp_prefix as grp_prefix,
                                            count(DISTINCT a.person_id) as cnt 
                                            from olimp_stages as s 
                                            left join olimp_exams as e on (e.id=s.exam) 
                                            left join olimp_forms as f on (f.id=s.form) 
                                            left join olimp_places as pl on (pl.id=s.place) 
                                            left join olimp_actions as a on (a.stage_id=s.id) 
                                            left join olimp_persons as p on (p.id=a.person_id) 
                                            where s.id =' . $id .
        ' group by stage, dt, place, form, exam, id');
    return mysql_fetch_assoc( $res );
}

function build_page_update( $row )
{
    $query_text = "";
    $flag = false;
    $res = "";

    /*if( $_POST['n_date_breg'] != "" && $_POST['n_date_ereg'] != "" &&
        $_POST['n_date_ereg_time'] != "" && $_POST['n_date_exam'] != "" &&
        $_POST['n_date_exam_time'] != "" && $_POST['n_date_ereg'] != "" )
    {    }
    if( $flag )
    {    }
    else
    {        $res .= '<span style="color: red">Необходимо заполнить все поля!</span>';    }
    */

    $res .= '<form action="" method="post" novalidate>
                 <table>
                      <tr>
                        <td><label for="exam">Предмет:</label></td>
                        <td><select name="n_exam" id="exam">';
    $q = mysql_query("select id, name from olimp_exams order by id;");

    while($r = mysql_fetch_row($q))
    {
        if ($row['exam'] == $r[1])
            $res .= '<option value="' . $r[0] . '" selected="selected">' . $r[1] . '</option>';
        else
            $res .= '<option value="' . $r[0] . '">' . $r[1] . '</option>';
    }
    $res .= ' </select></td>
                  </tr>                  
                  <br>
                  <tr>
                    <td><label for="classes">Классы:</label></td>
                    <td><select name="n_classes" id="classes">';
    if( $row[ 'school_class' ] == 7 )
    {
        $res .= '<option value="7" selected="selected" >7</option>
                     <option value="8">8</option>
                     <option value="9">9</option>
                     <option value="10">10</option>
                     <option value="11">11</option>';
    }
    else if( $row[ 'school_class' ] == 8 )
    {
        $res .= '<option value="7">7</option>
                     <option value="8" selected="selected">8</option>
                     <option value="9">9</option>
                     <option value="10">10</option>
                     <option value="11">11</option>';
    }
    else if( $row[ 'school_class' ] == 9 )
    {
        $res .= '<option value="7" >7</option>
                     <option value="8">8</option>
                     <option value="9" selected="selected">9</option>
                     <option value="10">10</option>
                     <option value="11">11</option>';
    }
    else if( $row[ 'school_class' ] == 10 )
    {
        $res .= '<option value="7">7</option>
                     <option value="8">8</option>
                     <option value="9">9</option>
                     <option value="10" selected="selected">10</option>
                     <option value="11">11</option>';
    }
    else if( $row[ 'school_class' ] == 11 )
    {
        $res .= '<option value="7">7</option>
                     <option value="8">8</option>
                     <option value="9">9</option>
                     <option value="10">10</option>
                     <option value="11" selected="selected">11</option>';
    }
    $res .= '</select></td>
                      </tr>
                    
                      <tr>
                        <td><label for="place">Площадка:</label></td>
                        <td><select name="n_place" id="place">';
    $q = mysql_query("select id, name from olimp_places order by abbr;");
    while($r = mysql_fetch_row($q))
    {
        if( $row['place'] == $r[1] )
            $res .= '<option value="'.$r[0].'" selected="selected">'.$r[1].'</option>';
        else
            $res .= '<option value="'.$r[0].'">'.$r[1].'</option>';

    }

    $res .= '
              </select></td>
              </tr>
              <br>
              <tr>
                <td><label for="stage">Этап:</label></td>
                <td><select name="n_stage" id="stage">';

    $q = mysql_query("select id, name from olimp_stage_types order by id;");
    while($r = mysql_fetch_row($q))
    {
        if( $row['stage'] == $r[0] )
            $res .= '<option value="'.$r[0].'" selected="selected">'.$r[1].'</option>';
        else
            $res .= '<option value="'.$r[0].'">'.$r[1].'</option>';
    }

    $res .= '
                </select>
                </td>
              </tr><br>
            
              <tr>
                <td><label for="form">Форма:</label></td>
                <td><select name="n_form" id="form">';

    $q = mysql_query("select id, name from olimp_forms order by id;");
    while($r = mysql_fetch_row($q))
    {
        if( $row['form'] == $r[1] )
            $res .= '<option value="'.$r[0].'" selected="selected">'.$r[1].'</option>';
        else
            $res .= '<option value="'.$r[0].'">'.$r[1].'</option>';

    }
    $res .= '
                </select></td>
              </tr>
            
              <tr>
                <td><label for="date_breg">Начало регистрации:</label></td>
                <td><input type="date" id="date_breg" name="n_date_breg" required value="'. $row['dt_breg'] .'"></td>
              </tr>
            
              <tr>
                <td><label for="date_ereg">Завершение регистрации:</label></td>
                <td><input type="date" id="date_ereg" name="n_date_ereg" required value = " '. $row['dt_ereg'] .' ">
                <input type="time" id="date_ereg_time" name="n_date_ereg_time" required ></td>
              </tr>
            
              <tr>
                <td><label for="date_exam">Дата проведения:</label></td>
                <td><input type="date" id="date_exam" name="n_date_exam" required value=" '. $row['dt'] .' ">
                <input type="time" id="date_exam_time" name="n_date_exam_time" required></td>
              </tr><br>
            
              <tr>
                <td><label id = "date_post_lableID" for="date_post">Дата отправки по почте:</label></td>
                <td><input type="date" id="date_post" name="n_date_post" required value="'. $row['dt_post']. '">
                <input type="time" id="date_post_time" name="n_date_post_time" required></td>
              </tr>
            
              <tr>
                <td><label for="grp_prefix">Префикс группы:</label></td>
                <td><input id="grp_prefix" name="n_grp_prefix" value="'.$row['grp_prefix'].'">';

    $res .= '
     
    </td>
  </tr>

  <tr>
    <td><label for="grp_limit">Количество в группе:</label></td>
    <td><input type="text" maxlength="11" id="grp_limit" name="n_grp_limit" value = " '. $row[ 'grp_limit' ] .' " ></td>
  </tr>

  <tr>
    <td><label id="var_prefix_lableID" for="var_prefix">Префикс варианта:</label></td>
    <td><input type="text" maxlength="8" id="var_prefix" name="n_var_prefix" value = "'. $row['prefix_var'] .'"></td>
  </tr>

  <tr>
    <td><label id="min_var_lableID" for="min_var">Начальный номер:</label></td>
    <td><input type="number" maxlength="11" id="min_var" name="n_min_var" value = " '. $row[ 'min_var' ] .' "></td>
  </tr>

  <tr>
    <td><label id="max_var_lableID" for="max_var">Конечный номер:</label></td>
    <td><input type="number" maxlength="11" id="max_var" name="n_max_var" value = " '. $row[ 'max_var' ] .' "></td>
  </tr>

  <tr>
    <td><label for="task_count">Количесво задач:</label></td>
    <td><input type="number" maxlength="11" id="task_count" name="n_task_count" value = " '. $row[ 'task_count' ] .' "></td>
  </tr>

  <tr>
    <td><label  for="task_weight">Веса задач:</label></td>
    <td>
        <input type="text" maxlength="128" id="task_weight" name="n_task_weight" value = " '. $row[ 'task_weight' ] .' ">
        <input type="hidden" name="id" value="'.$row['id'].'">
    </td>
  </tr>
  <tr><td colspan="2"><input id="submitBtn" type="submit" value="Изменить"></td></tr>
    </table>
    </form>
  

 <!--  <style>
  #date_post,#date_post_lableID,#date_post_time,#var_prefix,#min_var,#max_var, #max_var_lableID,
  #var_prefix_lableID,#min_var_lableID {
      display:   none;
  } 
  </style> -->
  
    <script>
  document.getElementById("form").onchange = function(){
    j = this.value;

    var d_p_l = document.getElementById("date_post_lableID");
    var d_p = document.getElementById("date_post");
    var d_p_t = document.getElementById("date_post_time");

    var v_p_l =  document.getElementById("var_prefix_lableID");
    var v_p =  document.getElementById("var_prefix");

    min_v_l = document.getElementById("min_var_lableID");
    min_v = document.getElementById("min_var");

    max_v_l = document.getElementById("max_var_lableID");
    max_v = document.getElementById("max_var");

    sB = document.getElementById("submitBtn");

    d_p_l.style.display = "none"; 
    d_p.style.display = "none";
    d_p_t.style.display = "none";

    v_p_l.style.display = "none";
    v_p.style.display = "none";

    min_v_l.style.display = "none";
    min_v.style.display = "none";

    max_v_l.style.display = "none";
    max_v.style.display = "none";

    sB.style.display = "none";  

    if (j==2) {
    d_p_l.style.display = "inline-block"; 
    d_p.style.display = "inline-block";
    d_p_t.style.display = "inline-block";

    v_p_l.style.display = "inline-block";
    v_p.style.display = "inline-block";

    min_v_l.style.display = "inline-block";
    min_v.style.display = "inline-block";

    max_v_l.style.display = "inline-block";
    max_v.style.display = "inline-block";

    sB.style.display = "inline-block";  
    }
    else sB.style.display = "inline-block";
    //alert(123);
  }
  </script>';

    return $res;
}

function build_page()
{
    Global $tpl,$refpar,$formrefpar,$sitemap,$uid,$role,$user,$pagemenu;
    $stages='';
    $even=0;
    $stage_dt='';
    $stage_place='';
    $stage_exam='';
    $res=mysql_query('select e.name as exam, s.id as id,
                                            p.school_class as school_class,
                                            pl.name as place,
                                            s.stage as stage,
                                            f.name as form, 
                                            DATE_FORMAT(s.date_exam,"%d.%m.%Y") as dt, 
                                            count(DISTINCT a.person_id) as cnt 
                                            from olimp_stages as s 
                                            left join olimp_exams as e on (e.id=s.exam) 
                                            left join olimp_forms as f on (f.id=s.form) 
                                            left join olimp_places as pl on (pl.id=s.place) 
                                            left join olimp_actions as a on (a.stage_id=s.id) 
                                            left join olimp_persons as p on (p.id=a.person_id) 
                                            group by stage, dt, place, form, exam, id');
    $i = 7;
    $stage_old = -1;
    while ($row=mysql_fetch_assoc($res))
    {
        if( $i == 12 )
        {
            $i = 7;
        }
        /*if (!isset($sta[$row['dt']])) $sta[$row['dt']]['rows']=0;
        //echo $row['dt'];
        if (isset($sta[$row['dt']][$row['place']][$row['exam']]))
        {
            $sta[$row['dt']][$row['place']][$row['exam']][$row['school_class']]+=$row['cnt'];
        }
        else
        { $sta[$row['dt']][$row['place']][$row['exam']]=array(7=>0,8=>0,9=>0,10=>0,11=>0);
            $sta[$row['dt']][$row['place']][$row['exam']][$row['school_class']]=$row['cnt'];
            $sta[$row['dt']][$row['place']][$row['exam']]['form']=$row['form'];
            $sta[$row['dt']][$row['place']][$row['exam']]['id']=$row['id'];
            $sta[$row['dt']]['stage']=$row['stage'];
            $sta[$row['dt']]['rows']+=1;
        }
        var_dump($row);*/
        if( $row[ 'cnt' ] > 0 )
        {
            if( $stage_old != $row[ 'stage' ] )
            {
                $stage_old = $row[ 'stage' ];
                if( $row[ 'stage' ] == 0 )
                {
                    $stages.=str_replace('%name%','Призеры прошлого года', $tpl['stages']['stage_tline']);
                }
                else if( $row[ 'stage' ] == 1 )
                {
                    $stages.=str_replace('%name%','Отборочный этап',$tpl['stages']['stage_tline']);
                }
                else if( $row[ 'stage' ] == 2 )
                {
                    $stages.=str_replace('%name%','Заключительный этап',$tpl['stages']['stage_tline']);
                }
                else if( $row[ 'stage' ] == 3 )
                {
                    $stages.=str_replace('%name%','Тренировочный этап',$tpl['stages']['stage_tline']);
                }
            }

            $stages .= str_replace(
                array('%dt_rows%', '%place_rows%', '%exam%', '%stage%', '%place%', '%form%', '%dt%',
                    '%even%',
                    '%num%',
                    '%school_class%'),
                array(1, 1, $row[ 'exam' ], $row[ 'stage' ], $row[ 'place' ], $row[ 'form' ], $row[ 'dt' ],
                    $even ? 'even' : 'odd',
                    $row[ 'id' ],
                    $i),
                $tpl['stages']['stage_line1']);
        }
        $even = !$even;
        $i++;
    }

    /*function draw_stage_list($sta,$stage)
    {
        global $tpl;
        $stages='';
        if (!is_array($sta)) return '';
        // добавить счетчик номера строки
        foreach ($sta as $date=>$v)
        {	if ($v['stage']!=$stage) continue;
            if (!is_array($v)) continue;
            $newdate=true;

            foreach ($v as $place=>$w)
            {
                if (!is_array($w)) continue;
                $newplace = true;
                $place_rows=count($w);
                $j=0;
                foreach ($w as $exam => $ww)
                {
                    //var_dump($ww);

                    for($i=7; $i<12; $i++)
                    {
                        if($ww[$i]>0)
                        {
                            $j++;
                        }
                    }
                    //echo $j;
                    $v['rows'] *= $j;
                    $place_rows *= $j;
                    $vr= $v['rows'];
                    $pr= $place_rows;
                    for($i=7; $i<12; $i++)
                    {// echo $ww[%i][id]
                        if($ww[$i]>0)
                        {
                            $stages .= str_replace(
                                array('%dt_rows%', '%place_rows%', '%exam%', '%stage%', '%place%', '%form%', '%dt%',
                                    '%even%',
                                    '%7%', '%8%', '%9%', '%10%', '%11%',
                                    '%num%',
                                    '%school_class%'),
                                array(1, 1, $exam, $ww['stage'], $place, $ww['form'], $date,
                                    $even ? 'even' : 'odd',
                                    $ww[7], $ww[8], $ww[9], $ww[10], $ww[11],
                                    $ww['id'],
                                    //$ww['school_class']
                                    $i),
                                $tpl['stages']['stage_line' . ($newdate ? '1' : ($newplace ? '2' : 'N'))]);
                        }
                    }
                    $even = !$even;
                }
            }
        }
        //echo $stages;
        return $stages;
    }*/
    /* $s=draw_stage_list($sta,2);
     if ($s) $stages.=str_replace('%name%','Заключительный этап',$tpl['stages']['stage_tline']).$s;
     $s=draw_stage_list($sta,0);
     if ($s) $stages.=str_replace('%name%','Призеры прошлого года',$tpl['stages']['stage_tline']).$s;
     $s=draw_stage_list($sta,3);
     if ($s) $stages.=str_replace('%name%','Тренировочный этап',$tpl['stages']['stage_tline']).$s;
     $s=draw_stage_list($sta,1);
     if ($s) $stages.=str_replace('%name%','Отборочный этап',$tpl['stages']['stage_tline']).$s;*/

    //echo count($stages);

    $body=str_replace(array('%stages%'),
        array( $stages),
        $tpl['stages']['stages_list']);
    return str_replace('%error%',$error,$body);
}

$tpl['stages']['stages_list']=
    '%error%
<style>
table#stages {border-top: 1px solid blue;border-left: 1px solid blue; border-collapse:collapse;}
#stages td {border-bottom: 1px solid blue;border-right: 1px solid blue; padding:4px 8px;}
#stages #ttl td {background: #003366; color:white; font-weight:bold; text-align: center;}
#stages .even td {background: #ccffff; color:#000066;}
#stages .odd td {background: #F0ffff; color:#000066;}
#stages .tline {background: #F0ffff; color:#000066; font-size:larger;}
#stages .sum {font-weight:bold;}
</style>

<form action="olimp_stages.html" method=post>
<br>

<table cellpadding=2 id="stages">
%stages%
</table>

<br>
<input type="submit" value="Добавить" onClick="location.href=\'olimp_stages_add.html\'">
<input type="submit" value="Удалить" onClick="deleteRows()">
<br>
</form>

<script>
function deleteRows() 
{ 
    var table_main = document.getElementById("stages");
    var trs = table_main.getElementsByTagName("tr");
    var date, place, exam, form, classes, stage;
    var olimp_stages_arr = [ ];
    var k = 0;
    //alert( trs.length );
    for( var i = 0; i < trs.length; i++ )
    {   
        //alert(tds.length);
        var tds = trs[ i ].getElementsByTagName( "td" );
        if( tds.length == 1 )
        {
            stage = tds[ 0 ].innerHTML;
            olimp_stages_arr.push(k, stage);
        }
        else if( tds.length == 8 )
        {
            date = tds[ 0 ].innerHTML;
            place = tds[ 1 ].innerHTML;
            olimp_stages_arr.push(date, place);
        }     
        else if( tds.length > 1 )
        {
            var input = trs[ i ].getElementsByTagName( "input" );
            if( input[0].checked == true )
            {
                exam = tds[ 2 ].innerHTML;
                classes = tds[ 4 ].innerHTML;
                olimp_stages_arr.push(exam, classes);
                k++;
            }
        }
    }  
} 

    /*var table_main = document.getElementById("stages");
    var trs = table_main.getElementsByTagName("tr");
    var date;
    var univer;
    for( var i = 0; i < trs.length; i++ )
    {
        
    }
        
    var i = inputs.length;
    while (i--) {
        var input = inputs[i];
        if (input.checked == true) {
            var tr = input.parentNode.parentNode;
            table_main.deleteRow(tr.rowIndex);
        }
    }
    if (input.checked == true) {
            var tr = input.parentNode.parentNode;
            var name = tr.getElementsByTagName("td")[0].childNodes[0].value;
            alert( name );
            //table_main.deleteRow(tr.rowIndex);
        }*/
</script>

<br><br>
<br><br>

';


$tpl['stages']['stage_line1']=
    '<tr class=%even%>
<td rowspan="%dt_rows%">%dt%</td>
<td rowspan="%place_rows%">%place%</td>
    <td>%exam%</td>
    <td>%form%</td>
    <td>%num%</td>
    <td>%school_class%</td>
    <td>Идет регистрация*</td>
    <td><input type="submit" name="box%num%" value="Изменить"></td>
    <td>
        <input type="checkbox" name="%num%">
    </td>
</tr>
';


$tpl['stages']['stage_tline']='<tr><td colspan=9 class=tline>%name%</td></tr>

';

$tpl['stages']['HTML']['header']='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//RU">
<title>Олимпиада школьников "Надежда энергетики". Списки участников.</title>
<style>
table 
{	border-collapse:collapse;
	border-top: 1px solid black;
	border-left: 1px solid black;
}

td 
{	border-bottom: 1px solid black;
	border-right: 1px solid black;
	padding:4px;
}
</style>

';

$tpl['stages']['stages_list10'] = '%error%
                                    <style>
                                    table#stages {border-top: 1px solid blue;border-left: 1px solid blue; border-collapse:collapse;}
                                    #stages td {border-bottom: 1px solid blue;border-right: 1px solid blue; padding:4px 8px;}
                                    #stages #ttl td {background: #003366; color:white; font-weight:bold; text-align: center;}
                                    #stages .even td {background: #ccffff; color:#000066;}
                                    #stages .odd td {background: #F0ffff; color:#000066;}
                                    #stages .tline {background: #F0ffff; color:#000066; font-size:larger;}
                                    #stages .sum {font-weight:bold;}
                                    </style>
                                    <form action="" method=post>
                                    
                                    <br>
                                    <table cellpadding=2 id="stages">
                                    %stages%
                                    </table>
                                    <br>
                                    
                                    
                                    <input type="button" value="Изменить" onClick="location.href=\'olimp_stages2.php\'">
                                    <br>
                                    </form>
                                    <br><br>
                                    
                                    
                                    <br><br>
                                    ';

$tpl['stages']['CSV']['header']='';

$key = key( $_POST );
$pos = strpos( $key, 'box' );
$pos2 = strpos( $key, 'n_exam' );
if( $pos !== false )
{
    $id = substr( $key, $pos + 3 );
    $row = get_row_bd( $id );
    $body = build_page_update( $row );
}
else if( $pos2 !== false )
{
    update_bd();
    $body=build_page();
}
else
{
    delete_rows( );
    $body=build_page();
}

?>