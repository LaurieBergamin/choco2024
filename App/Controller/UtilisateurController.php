<?php
namespace App\Controller;
use App\Model\Utilisateur;
use App\Utils\Utilitaire;
use App\Vue\Template;
class UtilisateurController extends Utilisateur{
    //Méthodes
    public function addUtilisateur(): void{
            $error = '';
            if(isset($_POST['submit'])){
            if(!empty($_POST['nom_utilisateur']) AND!empty($_POST["prenom_utilisateur"]) AND
            !empty($_POST["mail_utilisateur"]) AND !empty($_POST["password_utilisateur"])
            AND !empty($_POST["confirm_password"])){
                if($_POST["password_utilisateur"]==$_POST["confirm_password"]){
                    $mail=Utilitaire::cleanInput($_POST["mail_utilisateur"]);
                    $this->setMail($mail);
                    //test si compte n'existe pas
                    if (!$this->getUtilisateurByMail()){
                    //hasher mdp
                    $hash = password_hash(Utilitaire::cleanInput($_POST["password_utilisateur"]), 
                    PASSWORD_DEFAULT);
                    //On nettoie les entrées:
                    $nom=Utilitaire::cleanInput($_POST["nom_utilisateur"]);
                    $prenom=Utilitaire::cleanInput($_POST["prenom_utilisateur"]);
                    $mail=Utilitaire::cleanInput($_POST["mail_utilisateur"]);
                    $image='./public/asset/image/defaut.png';
                    //test si user a importé un fichier
                    if(!empty($_FILES["image_utilisateur"]["tmp_name"])){
                    //recup extension fichier
                        $ext = Utilitaire::getFileExtension($_FILES["image_utilisateur"]["name"]);
                    //renommer fichier
                        $name_image = uniqid().$ext;
                    //remplacer la variable image
                        $image= "./Public/asset/images/".$name_image;
                        //déplacer le fichier
                        move_uploaded_file($_FILES["image_utilisateur"]["tmp_name"],$image);
                    }
                    
                    //setter valeurs dans objet utilisateur:
                    $this->setNom($nom);
                    $this->setPrenom($prenom);
                    $this->setMail($mail);
                    $this->setPassword($hash);
                    $this->setImage($image);
                    $this->getRoles()->setId(1);
                    $this->insertUtilisateur();
                    $error = "Le compte a été ajouté en BDD";
                }
                //test si compte existe
            else{
                $error = 'Les informations sont incorrectes';
            }
        }
            //cas mdp différents
            else{
                $error = "Les mots de passe sont différents";
            }    

        }   
            //test champs pas remplis
            else{
            $error = "Veuillez remplir tous les champs du formulaire";
            }
        }
    //renvoi partie html:
            Template::render('navbar.php', 'Inscription', 'vueAdduser.php', 'footer.php', 
            $error, [], ['main.css']);
    }
        public function connexionUtilisateur(): void{
            $error = "";
            //test si bouton cliqué
            if (isset($_POST["submit"])){
                //test si chps remplis
                if(!empty($_POST["mail_utilisateur"]) AND !empty($_POST["password_utilisateur"])){
                    $mail=Utilitaire::cleanInput($_POST["mail_utilisateur"]);
                    $this->setMail($mail);
                    $recup=$this->getUtilisateurByMail();
                    if($recup){
                        //test si mdp est valide
                        $hash = $recup->getPassword();
                        $password = Utilitaire::cleanInput($_POST["password_utilisateur"]);
                        //tester mdp valide
                        if(password_verify($password, $hash)){
                            //créer session
                            $_SESSION["connected"] = true;
                            $_SESSION["nom"] = $recup->getNom;
                            $_SESSION["prenom"] = $recup->getprenom;
                            $_SESSION["image"] = $recup->getImage();
                            $_SESSION["id"] = $recup->getId();
                            $error = "Connecté";
                        }
                        else{
                        //test si password invalide
                        $error = "Les informations de connexion sont invalides";
                        }
                    }
                //test si compte n'existe pas
                else {
                    $error = "Les informations de connexion sont invalides";
                }
            }
            else{
                $error="Veuillez remplir tous les champs du formulaire";
                }
            }
            Template::render('navbar.php', 'Connexion', 'vueConnexion.php', 'footer.php', 
            $error, [], ['main.css']);
            }
    }   

