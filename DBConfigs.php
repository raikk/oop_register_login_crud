<?php
require 'functions.php';
session_start();
$DB_host = "localhost";
$DB_user = "root";
$DB_pass = "";
$DB_name = "task_manager";
try
{
     $DB_con = new PDO("mysql:host={$DB_host};dbname={$DB_name}",$DB_user,$DB_pass);
     $DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
     echo $e->getMessage();
}
include_once 'Db_handlers.php';
include_once 'userstask.php';
$db = new DbHandler($DB_con);
$task = new Usertask($DB_con);