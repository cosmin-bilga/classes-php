<?php
include "user.php";
include "user-pdo.php";

$user = new User();
//$user = new Userpdo();


print_r($user->register("Amine", "mdp", "amine@example.com", "Vlad", "Bilga"));
$user->connect('Amine', 'mdp');
print_r($user->getAllInfos());
$user->update("Amine2", "mdp", "amine@example.com", "Vlad", "Bilga");
echo $user->getLogin();
echo $user->getEmail();
echo $user->getFirstname();




//$user->register("Dodo", "mdp", "dodo@example.com", "dodo", "dodo");
//$user->connect('Dodo2', 'mdp');
//$user->update("Dodo2", "mdp", "dodo@example.com", "dodo", "dodo");
//print_r($user->getAllInfos());
