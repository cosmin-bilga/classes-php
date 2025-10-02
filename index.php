<?php
include "user.php";
include "user-pdo.php";

//$user = new User();
//$user = new Userpdo();


//print_r($user->register("Clad2", "mdp", "clad2@example.com", "Vlad", "Bilga"));
$user->connect('Clad3', 'mdp');
print_r($user->getAllInfos());
//$user->update("Clad3", "mdp", "clad3@example.com", "Vlad", "Bilga");
echo $user->getLogin();
echo $user->getEmail();
echo $user->getFirstName();




//$user->register("Dodo", "mdp", "dodo@example.com", "dodo", "dodo");
//$user->connect('Dodo2', 'mdp');
//$user->update("Dodo2", "mdp", "dodo@example.com", "dodo", "dodo");
//print_r($user->getAllInfos());
