<?php

// Nom du fichier doit etre le meme que le nome de la class pour le autoload
declare(strict_types=1);
require_once __DIR__ . '/autoload.php';

class Userpdo
{

    use \Domain\Database\ServerConnection;

    private int $id;
    public string $login;
    public string $email;
    public string $firstname;
    public string $lastname;
    private PDO $conn;

    public function __construct()
    {
        try {
            $this->conn = new PDO(
                "mysql:host=$this->server;dbname=$this->database;charset=$this->charset;port=$this->port",
                $this->user,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $this->disconnect();
    }

    /**
     * Crée l’utilisateur en base de donnée dans la table “utilisateurs”.
     * Retourne un tableau contenant l'ensemble des informations de ce même utilisateur.
     */
    public function register(string $login, string $password, string $email, string $firstname, string $lastname): array
    {
        try {
            $sql = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (:login, :password, :email, :firstname, :lastname);";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':login' => $login,
                ':password' => $password,
                ':email' => $email,
                ':firstname' => $firstname,
                ':lastname' => $lastname
            ]);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }

        return [
            "login" => $login,
            "password" => $password,
            "email" => $email,
            "firstname" => $firstname,
            "lastname" => $lastname
        ];
    }

    /**
     * Connecte l’utilisateur, et donne aux attributs de la classe les valeurs
     * correspondantes à celles de l’utilisateur connecté.
     */
    public function connect(string $login, string $password): void
    {
        try {
            $sql = "SELECT * FROM utilisateurs WHERE login=:login;";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':login' => $login
            ]);
            $res = $stmt->fetch();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return;
        }
        if (!isset($res) or $res === false)
            return;
        //print_r($res);
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
     * Supprime ET déconnecte un user
     */
    public function delete(): void
    {
        try {
            $sql = "DELETE * FROM utilisateurs WHERE id=:id;";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id' => $this->id
            ]);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $this->disconnect();
    }


    /**
     * Met à jour les attributs de l’objet, et modifie les informations en base de données.
     */
    public function update(string $login, string $password, string $email, string $firstname, string $lastname): void
    {
        try {
            $sql = "UPDATE utilisateurs SET login=:login , password=:password, email=:email, firstname=:firstname, lastname=:lastname WHERE login=:curr_login;";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':login' => $login,
                ':password' => $password,
                ':email' => $email,
                ':firstname' => $firstname,
                ':lastname' => $lastname,
                ':curr_login' => $this->login,
            ]);
        } catch (PDOException $e) {
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
     * Retourne un booléen (true ou false) permettant de savoir si un utilisateur est connecté ou non
     */
    public function isConnected(): bool
    {
        if ($this->id === -1)
            return false;
        return true;
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
