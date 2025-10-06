<?php

declare(strict_types=1);
require_once __DIR__ . '/autoload.php';

class User
{
    use \Domain\Database\ServerConnection;

    private int $id;
    public string $login;
    public string $email;
    public string $firstname;
    public string $lastname;
    private mysqli $conn;


    /**
     * Connexion à la DB
     */
    public function __construct()
    {
        //echo $this->server, $this->user, $this->password, $this->database, $this->port;
        $this->conn = new mysqli(
            $this->server,
            $this->user,
            $this->password,
            $this->database,
            $this->port
        );
        $this->conn->set_charset($this->charset);


        if ($this->conn->connect_errno) {
            die("Connection to database failed: " . $this->conn->connect_error);
        }
        //disconnect remet à 0 tous les variables de l'objet, on l'utilise pour initialiser
        $this->disconnect();
    }


    /**
     * Crée l’utilisateur en base de donnée dans la table “utilisateurs”.
     * Retourne un tableau contenant l'ensemble des informations de ce même utilisateur.
     */
    public function register(string $login, string $password, string $email, string $firstname, string $lastname): array
    {
        try {
            $sql = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?);";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssss", $login, $password, $email, $firstname, $lastname);
            $stmt->execute();
            return [
                "login" => $login,
                "password" => $password,
                "email" => $email,
                "firstname" => $firstname,
                "lastname" => $lastname
            ];
        } catch (mysqli_sql_exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    /**
     * Connecte l’utilisateur, et donne aux attributs de la classe les valeurs
     * correspondantes à celles de l’utilisateur connecté.
     */
    public function connect(string $login, string $password): void
    {
        try {
            $sql = "SELECT * FROM utilisateurs WHERE login=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $res = $stmt->get_result();
            $res = mysqli_fetch_array($res);
            //print_r($res);
        } catch (mysqli_sql_exception $e) {
            echo $e->getMessage();
            return;
        }
        if (!isset($res))
            return;

        $db_password = $res['password'];
        if ($password === $db_password) {
            $this->id = (int)$res['id'];
            $this->login = $res['login'];
            $this->password = $res['password'];
            $this->email = $res['email'];
            $this->firstname = $res['firstname'];
            $this->lastname = $res['lastname'];
        }
    }

    /**
     * Déconnecte l’utilisateur
     */
    public function disconnect(): void
    {
        $this->id = -1;
        $this->login = '';
        $this->password = '';
        $this->email = '';
        $this->firstname = '';
        $this->lastname = '';
    }

    /**
     * Supprime ET déconnecte un user
     */
    public function delete(): void
    {
        try {
            $sql = "DELETE * FROM utilisateurs WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $this->id);
            $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            echo $e->getMessage();
        }
        $this->disconnect();
    }


    /**
     * Met à jour les attributs de l’objet, et modifie les informations en base de données.
     */
    public function update(string $login, string $password, string $email, string $firstname, string $lastname): void
    {
        if (!$this->isConnected())
            return;
        try {
            $sql = "UPDATE utilisateurs SET login=? , password=?, email=?, firstname=?, lastname=? WHERE login=?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssssss", $login, $password, $email, $firstname, $lastname, $this->login);
            $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            echo $e->getMessage();
            return;
        }
        $this->login = $login;
        $this->password = $password;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    /**
     * Retourne un booléen (true ou false) permettant de savoir si un utilisateur est connecté ou non
     */
    public function isConnected(): bool
    {
        if ($this->id === -1)
            return false;
        return true;
    }

    /**
     * Retourne un tableau contenant l’ensemble des informations de l’utilisateur
     */
    public function getAllInfos(): array
    {
        return [
            "id" => $this->id,
            "login" => $this->login,
            "email" => $this->email,
            "password" => $this->password,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname
        ];
    }

    /**
     * Retourne le login de l’utilisateur
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * Retourne l’email de l’utilisateur
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Retourne le firstname de l’utilisateur
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * Retourne le lastname de l’utilisateur
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }
}
