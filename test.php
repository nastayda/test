<?php
include_once('olimp_common.inc');
var_dump($_POST);
if($_POST['data'])
{
    echo json_decode("3");
}
else{
    function build_page()
    {
        Global $tpl, $refpar, $formrefpar, $sitemap, $uid, $role, $user, $pagemenu;

        $user_right = check_user_right(3);
        $res = "";

        if (!$user_right['Access']) {
            return $tpl['noaccess'];
        }

        $flag = false;
        $query_text = "";
        /* if($_POST['n_date_breg'] != "" && $_POST['n_date_ereg'] != "" &&
           $_POST['n_date_ereg_time'] != "" && $_POST['n_date_exam'] != "" &&
           $_POST['n_date_exam_time'] != "" && $_POST['n_date_post'] != "" &&
           $_POST['n_date_post_time'] != "" && $_POST['n_date_ereg'] != "")
         {*/
        if($_POST['n_date_breg'] != "" && $_POST['n_date_ereg'] != "" &&
            $_POST['n_date_ereg_time'] != "" && $_POST['n_date_exam'] != "" &&
            $_POST['n_date_exam_time'] != "" && $_POST['n_date_ereg'] != "")
        {

            if($_POST['n_min_var'] != "")
                $n_min_var = (int)$_POST['n_min_var'];
            else
                $n_min_var = 1;

            if($_POST['n_max_var'] != "")
                $n_max_var = (int)$_POST['n_max_var'];
            else
                $n_max_var = 1;

            if($_POST['n_task_count'] != "")
                $n_task_count = (int)$_POST['n_task_count'];
            else
                $n_task_count = 5;

            $query_text = "select name from olimp_groups where id=".(+$_POST['n_grp_prefix']).";";
            $q = mysql_query($query_text);
            $grp_name = mysql_fetch_row($q);
            $a = getdate();
            $query_text = "INSERT INTO `olimp_stages` (`id`, `exam`, `classes`, `place`, `limit`, `regs`, `year`, `stage`, 
                                                       `form`, `date_breg`, `date_ereg`, `date_exam`, `date_post`, `grp_prefix`, 
                                                       `grp_limit`, `next_group`, `var_prefix`, `min_var`, `max_var`, `descr`, `task_count`, 
                                                       `results_ready`, `cipher`, `task_weight`, `use_person_result_text`, `stage_result_text`) 
                           VALUES (NULL, ".($_POST['n_exam']).", '".($_POST['n_classes'])."', ".($_POST['n_place']).", 0, 0, ".($a['year']).", 
                                         ".($_POST['n_stage']).", ".($_POST['n_form']).", '".($_POST['n_date_breg'])."',
                                         '".($_POST['n_date_ereg'])." ".($_POST['n_date_ereg_time'])."', 
                                         '".($_POST['n_date_exam'])." ".($_POST['n_date_exam_time'])."', 
                                         '".($_POST['n_date_post'])." ".($_POST['n_date_post_time'])."', 
                                         '".($grp_name[0])."', 251, 1, '".($_POST['n_var_prefix'])."', 
                                         ".($n_min_var).", ".($n_max_var).", '', ".($n_task_count).", 
                                         'N', '', '', '".($_POST['n_task_weight'])."', '');";
            $flag = true;
        }

        else { echo $_POST['n_date_breg'];
            $flag = false;
        }

        if($flag) {
            mysql_query($query_text);
            echo mysql_error();
            $res .= '<span style="color: green">Запись добавлена!</span>';
        }
        else {
            $res .= '<span style="color: red">Необходимо заполнить все поля!</span>';
        }

        $res .= '<form action="olimp_stages_add.html" method="post" novalidate>
    <table>
      <tr>
        <td><label for="exam">Предмет:</label></td>
        <td><select name="n_exam" id="exam">';
        $q = mysql_query("select id, name from olimp_exams order by id;");
        while($r = mysql_fetch_row($q))
        {
            $res .= '<option value="'.$r[0].'">'.$r[1].'</option>';
        }
        $res .= '
        </select></td>
      </tr>
      
      <br>
      <tr>
        <td><label for="classes">Классы:</label></td>
        <td><select name="n_classes" id="classes">
          <option value="7">7</option>
          <option value="8">8</option>
          <option value="9">9</option>
          <option value="10">10</option>
          <option value="11">11</option>
        </select></td>
      </tr>

      <tr>
        <td><label for="place">Площадка:</label></td>
        <td><select name="n_place" id="place">';
        $q = mysql_query("select id, name from olimp_places order by abbr;");
        while($r = mysql_fetch_row($q))
        {
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
            $res .= '<option value="'.$r[0].'">'.$r[1].'</option>';
        }
        $res .= '
        </select></td>
      </tr>
	
      <tr>
        <td><label for="date_breg">Начало регистрации:</label></td>
        <td><input type="date" id="date_breg" name="n_date_breg" required></td>
      </tr>

      <tr>
        <td><label for="date_ereg">Завершение регистрации:</label></td>
        <td><input type="date" id="date_ereg" name="n_date_ereg" required>
        <input type="time" id="date_ereg_time" name="n_date_ereg_time" required></td>
      </tr>

      <tr>
        <td><label for="date_exam">Дата проведения:</label></td>
        <td><input type="date" id="date_exam" name="n_date_exam" required>
        <input type="time" id="date_exam_time" name="n_date_exam_time" required></td>
      </tr><br>

      <tr>
        <td><label id = "date_post_lableID" for="date_post">Дата отправки по почте:</label></td>
        <td><input type="date" id="date_post" name="n_date_post" required>
        <input type="time" id="date_post_time" name="n_date_post_time" required></td>
      </tr>

      <tr>
        <td><label for="grp_prefix">Префикс группы:</label></td>
        <td><input id="grp_prefix" name="n_grp_prefix">';


        /* $q = mysql_query("select id, name from olimp_groups order by id;");

         while($r = mysql_fetch_row($q))
         {
             $res .= '<option value="'.$r[0].'">'.$r[1].'</option>';
         }// </select>*/

        $res .= '
		 
        </td>
      </tr>

      <tr>
        <td><label for="grp_limit">Количество в группе:</label></td>
        <td><input type="text" maxlength="11" id="grp_limit" name="n_grp_limit"></td>
      </tr>

      <tr>
        <td><label id="var_prefix_lableID" for="var_prefix">Префикс варианта:</label></td>
        <td><input type="text" maxlength="8" id="var_prefix" name="n_var_prefix"></td>
      </tr>

      <tr>
        <td><label id="min_var_lableID" for="min_var">Начальный номер:</label></td>
        <td><input type="number" maxlength="11" id="min_var" name="n_min_var"></td>
      </tr>

      <tr>
        <td><label id="max_var_lableID" for="max_var">Конечный номер:</label></td>
        <td><input type="number" maxlength="11" id="max_var" name="n_max_var"></td>
      </tr>

      <tr>
        <td><label for="task_count">Количесво задач:</label></td>
        <td><input type="number" maxlength="11" id="task_count" name="n_task_count"></td>
      </tr>

      <tr>
        <td><label  for="task_weight">Веса задач:</label></td>
        <td><input type="text" maxlength="128" id="task_weight" name="n_task_weight"></td>
      </tr>
      <tr><td colspan="2"><input id="submitBtn" type="submit" value="Отправить"></td></tr>
    </table>
  </form>
  

 <style>
  #date_post,#date_post_lableID,#date_post_time,#var_prefix,#min_var,#max_var, #max_var_lableID,
  #var_prefix_lableID,#min_var_lableID {
      display:   none;
  } 
  </style>
  
<script>
  document.getElementById("form").onchange = function()
  {
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

    //sB = document.getElementById("submitBtn");

    d_p_l.style.display = "none"; 
    d_p.style.display = "none";
    d_p_t.style.display = "none";

    v_p_l.style.display = "none";
    v_p.style.display = "none";

    min_v_l.style.display = "none";
    min_v.style.display = "none";

    max_v_l.style.display = "none";
    max_v.style.display = "none";

    //sB.style.display = "none";  

    if (j==2) 
    {
    d_p_l.style.display = "inline-block"; 
    d_p.style.display = "inline-block";
    d_p_t.style.display = "inline-block";

    v_p_l.style.display = "inline-block";
    v_p.style.display = "inline-block";

    min_v_l.style.display = "inline-block";
    min_v.style.display = "inline-block";

    max_v_l.style.display = "inline-block";
    max_v.style.display = "inline-block";

    //sB.style.display = "inline-block";  
    }
    else {}//sB.style.display = "inline-block";
    //alert(123);
  }
  </script>';

        return $res;
    }


    $tpl['stages']['stages_list'] = '%error%
<style>
table#stages {border-top: 1px solid blue;border-left: 1px solid blue; border-collapse:collapse;}
#stages td {border-bottom: 1px solid blue;border-right: 1px solid blue; padding:4px 8px;}
#stages #ttl td {background: #003366; color:white; font-weight:bold; text-align: center;}
#stages .even td {background: #ccffff; color:#000066;}
#stages .odd td {background: #F0ffff; color:#000066;}
#stages .tline {background: #F0ffff; color:#000066; font-size:larger;}
#stages .sum {font-weight:bold;}
</style>
<form action="?" method=post>

<br>
<table cellpadding=2 id="stages">
%stages%
</table>
<br>

<!--
<table>
<tr>
    <td>Площадка:&nbsp;</td>  
       <td><select name="filter_place" size=0>%filter_places%</select>
   </td>
</tr>
<tr>
<td> Форма:&nbsp;</td>
      <td><select name="filter_form" size=0>%filter_forms%</select></td>
      <td rowspan=3><button type=submit name="btnFilter">
<br>Добавить<br><br></button></td></tr>

<tr><td>Предмет:&nbsp;</td>
       <td><select name="filter_exam" size=0>%filter_exams%</select></td>
</tr>
<tr>
    <td> Этап:&nbsp;</td>
   <td><select name="filter_stage" size=0>%filter_stages%</select></td></tr>

<tr>
<td> Дата:&nbsp;</td><td>
<input type="date" name="calendar"></td></tr>
</table>
-->

<input type="button" value="Добавить" onClick="location.href=\'olimp_stages2.php\'">
<br>
</form>
<br><br>


<br><br>
';


    /*    $tpl['stages']['stage_line1'] = '<tr class=%even%><td rowspan="%dt_rows%">%dt%</td><td rowspan="%place_rows%">%place%</td><td>%exam%</td><td>%form%</td><td>7</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>
    <tr class=%even%><td>%exam%</td><td>%form%</td><td>8</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>
    <tr class=%even%><td>%exam%</td><td>%form%</td><td>9</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>
    <tr class=%even%><td>%exam%</td><td>%form%</td><td>10</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>
    <tr class=%even%><td>%exam%</td><td>%form%</td><td>11</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>

    ';
        $tpl['stages']['stage_line2'] = '<tr class=%even%><td rowspan="%place_rows%">%place%</td><td>%exam%</td><td>%form%</td><td>7</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>
    <tr class=%even%><td>%exam%</td><td>%form%</td><td>8</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>
    <tr class=%even%><td>%exam%</td><td>%form%</td><td>9</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>
    <tr class=%even%><td>%exam%</td><td>%form%</td><td>10</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>
    <tr class=%even%><td>%exam%</td><td>%form%</td><td>11</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>
    ';
        $tpl['stages']['stage_lineN'] = '<tr class=%even%><td>%exam%</td><td>%form%</td><td>7</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>
    <tr class=%even%><td>%exam%</td><td>%form%</td><td>8</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>
    <tr class=%even%><td>%exam%</td><td>%form%</td><td>9</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>
    <tr class=%even%><td>%exam%</td><td>%form%</td><td>10</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>
    <tr class=%even%><td>%exam%</td><td>%form%</td><td>11</td><td>Идет регистрация*</td><td><b>[Изменить]</b></td><td><input type="checkbox"></td></tr>
    ';


        $tpl['stages']['stage_tline'] = '<tr><td colspan=9 class=tline>%name%</td></tr>';

        $tpl['stages']['HTML']['list_dt_line'] = '<h1>Потоки %list_dt%</h1>';
        $tpl['stages']['CSV']['list_dt_line']  = "%list_dt%\n";

        $tpl['stages']['HTML']['list_place_line'] = '<h1>Площадка %list_place%</h1><h2>%stage% %form% форма %list_dt%</h2>';
        $tpl['stages']['CSV']['list_place_line']  = "\"%list_place%\";\"%stage%\";\"%form%\";%list_dt%\n";

        $tpl['stages']['HTML']['list_exam_line'] = '<h1>%list_exam%</h1></h2>';
        $tpl['stages']['CSV']['list_exam_line']  = "\"%list_exam%\"\n";

        $tpl['stages']['HTML']['list']             = '<table><tr><td>#</td><td>ФИО</td><td>Д.Р.</td></tr>%list%</table>';
        $tpl['stages']['HTML']['list_school']      = '<table><tr><td>#</td><td>ФИО</td><td>Д.Р.</td><td>Школа</td><td>Адрес школы</td></tr>%list%</table>';
        $tpl['stages']['HTML']['list_addr']        = '<table><tr><td>#</td><td>ФИО</td><td>Д.Р.</td><td>Адрес регистрации</td></tr>%list%</table>';
        $tpl['stages']['HTML']['list_school_addr'] = '<table><tr><td>#</td><td>ФИО</td><td>Д.Р.</td><td>Школа</td><td>Адрес школы</td><td>Адрес регистрации</td></tr>%list%</table>';
        $tpl['stages']['CSV']['list']              = '%list%';
        $tpl['stages']['CSV']['list_school']       = '%list%';
        $tpl['stages']['CSV']['list_addr']         = '%list%';
        $tpl['stages']['CSV']['list_school_addr']  = '%list%';

        $tpl['stages']['HTML']['list_class_line'] = '<h1>%list_class% класс</h1></h2>';
        $tpl['stages']['CSV']['list_class_line']  = "\"%list_class% класс\"\n";


        $tpl['stages']['HTML']['list_school_addr_line'] = '<tr><td>%num%</td><td>%fio%</td><td>%birth%</td><td>%school%</td><td>%sc_addr%</td><td>%p_addr%</td></tr>';
        $tpl['stages']['CSV']['list_school_addr_line']  = "%pid%;\"%fio%\";\"%birth%\";\"%school%\";\"%sc_addr%\";\"%p_addr%\"\n";

        $tpl['stages']['HTML']['list_school_line'] = '<tr><td>%num%</td><td>%fio%</td><td>%birth%</td><td>%school%</td><td>%sc_addr%</td></tr>';
        $tpl['stages']['CSV']['list_school_line']  = "%pid%;\"%fio%\";\"%birth%\";\"%school%\";\"%sc_addr%\"\n";

        $tpl['stages']['HTML']['list_addr_line'] = '<tr><td>%num%</td><td>%fio%</td><td>%birth%</td><td>%p_addr%</td></tr>';
        $tpl['stages']['CSV']['list_addr_line']  = "%pid%;\"%fio%\";\"%birth%\";\"%p_addr%\"\n";

        $tpl['stages']['HTML']['list_line'] = '<tr><td>%num%</td><td>%fio%</td><td>%birth%</td></tr>';
        $tpl['stages']['CSV']['list_line']  = "%pid%;\"%fio%\";\"%birth%\"\n";
    */
    $tpl['stages']['HTML']['header'] = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//RU">
<title>Олимпиада школьников "Надежда энергетики". Списки участников.</title>
<style>
table 
{    border-collapse:collapse;
    border-top: 1px solid black;
    border-left: 1px solid black;
}

td 
{    border-bottom: 1px solid black;
    border-right: 1px solid black;
    padding:4px;
}
</style>

';
    $tpl['stages']['CSV']['header']  = '';

    $body = build_page();
}
?><?php
/**
 * Created by PhpStorm.
 * User: anastasiadanilkina
 * Date: 20.09.17
 * Time: 21:04
 */