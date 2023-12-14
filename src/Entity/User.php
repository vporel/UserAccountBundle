<?php
namespace UserAccountBundle\Entity;

use RootBundle\Entity\Trait\TimestampsTrait;
use UserAccountBundle\Validator\PhoneNumber;
use RootBundle\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\PasswordHasher\Hasher\MessageDigestPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Metadata as Api;
use RootBundle\Entity\Interface\EmailInterface;

/**
 * @ORM\MappedSuperclass
 * @author Vivian NKOUANANG (https://github.com/vporel) <dev.vporel@gmail.com>
 */
#[
    Api\ApiResource(security: "is_granted('ROLE_USER')"),
    Api\Post(
        routeName: "api.account.sendemailvalidationcode",
        openapiContext: [
            "summary" => "Envoyer un mail contenant le code pour la validation de l'adresse email", "description" => "",
            "requestBody" => ["content" => ["application/json" => []]],
            "responses" => ["200" => ["content" => ["application/json" => []]]]
        ]
    ),
    Api\Post(
        routeName: "api.account.validateemail",
        openapiContext: [
            "summary" => "Valider l'adresse email avec le code reçu par mail", "description" => "",
            "requestBody" => ["content" => ["application/json" => ["schema" => ["type" => "object", "properties" => [
                "code" => ["type" => "integer", "description" => "Code à valider", "required" => true]
            ]]]]],
            "responses" => ["200" => ["content" => ["application/json" => []]]]
        ]
    ),
    Api\Post(
        routeName: "api.account.changeemail",
        openapiContext: [
            "summary" => "Modifier l'adresse email", "description" => "",
            "requestBody" => ["content" => ["application/json" => ["schema" => ["type" => "object", "properties" => [
                "email" => ["type" => "string", "required" => true],
            ]]]]],
            "responses" => ["200" => ["content" => ["application/json" => []]]]
        ]
    ),
    Api\Post(
        routeName: "api.account.changepassword",
        openapiContext: [
            "summary" => "Modifier le mot de passe", "description" => "",
            "requestBody" => ["content" => ["application/json" => ["schema" => ["type" => "object", "properties" => [
                "currentPassword" => ["type" => "string", "description" => "Mot de passe actuel", "required" => true],
                "newPassword" => ["type" => "string", "description" => "Nouveau mot de passe", "required" => true]
            ]]]]],
            "responses" => ["200" => ["content" => ["application/json" => []]]]
        ] 
    ),
    Api\Post(
        routeName: "api.account.changeprofilephoto",
        openapiContext: [
            "summary" => "Modifier la photo de profil", "description" => "",
            "requestBody" => ["content" => ["application/json" => ["schema" => ["type" => "object", "properties" => [
                "file" => ["type" => "string", "format" => "byte", "description" => "La photo", "required" => true],
            ]]]]],
            "responses" => ["200" => ["content" => ["application/json" => ["schema" => ["type" => "object", "properties" => [
                "avatarFile" => ["type" => "string"],
                "avatarUrl" => ["type" => "string"],
            ]]]]]]
        ] 
    ),
    Api\Post(
        routeName: "api.account.sendpasswordresetcode",
        openapiContext: [
            "summary" => "Envoi par mail d'un code pour la réinitialisation du mot de passe", "description" => "",
            "requestBody" => ["content" => ["application/json" => ["schema" => ["type" => "object", "properties" => [
                "email" => ["type" => "integer", "description" => "Email du destinataire", "required" => true]
            ]]]]],
            "responses" => ["200" => ["content" => ["application/json" => []]]]
        ]
    ),
    Api\Post(
        routeName: "api.account.checkpasswordresetcode",
        openapiContext: [
            "summary" => "Vérifier le code reçu par mail", "description" => "",
            "requestBody" => ["content" => ["application/json" => ["schema" => ["type" => "object", "properties" => [
                "code" => ["type" => "integer", "description" => "Code à valider", "required" => true]
            ]]]]],
            "responses" => ["200" => ["content" => ["application/json" => []]]]
        ]
    ),
    Api\Post(
        routeName: "api.account.resetpassword",
        openapiContext: [
            "summary" => "Vérifier le code reçu par mail", "description" => "",
            "requestBody" => ["content" => ["application/json" => ["schema" => ["type" => "object", "properties" => [
                "code" => ["type" => "integer", "description" => "Code de réinitialisation reçu par mail", "required" => true],
                "password" => ["type" => "integer", "description" => "Nouveau mot de passe", "required" => true]
            ]]]]],
            "responses" => ["200" => ["content" => ["application/json" => []]]]
        ]
    ),
]
abstract class User extends Entity implements UserInterface, PasswordAuthenticatedUserInterface, EmailInterface{
    public const GENDER_MAN = 1;
    public const GENDER_WOMAN = 2;
    public const GENDERS = [
        self::GENDER_MAN => "Man",
        self::GENDER_WOMAN => "Woman"
    ];

    const DEFAULT_MAN_AVATAR_URL = "/images/users-avatars/man.png";
    const DEFAULT_WOMAN_AVATAR_URL = "/images/users-avatars/woman.png";

    public const AVATARS_FOLDER = "/uploads/users-avatars";
    public const AVATARS_EXTENSIONS = [".jpg", ".png", ".jpeg"];

    use TimestampsTrait;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    #[Assert\Length(min:3, minMessage:"Au moins 6 caractères")]
    #[Serializer\Groups(["user:read:collection", "user:read:simplified", "user:update"])]
    protected $firstName;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    #[Assert\Length(min:3, minMessage:"Au moins 6 caractères")]
    #[Serializer\Groups(["user:read:collection", "user:read:simplified", "user:update"])]
    protected $lastName;

    /**
     * @var ?string
     * @ORM\Column(type="string", nullable=true)
     */
    #[Serializer\Groups(["user:read:collection"])]
    protected $avatarFile;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\Email()]
    #[Serializer\Groups(["user:read:owner", "user:update"])]
    protected $email;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    #[Serializer\Groups(["user:read:owner"])]
    protected $emailValidated = false;

     /**
     * @ORM\Column(type="string")
     */
    #[Assert\Length(min:6, minMessage:"Au moins 6 caractères")]
    #[Serializer\Groups(["user:create"])]
    protected $password;

     /**
     * @ORM\Column(type="string", length=25)
     */
    #[Assert\Length(min:5, minMessage:"Au moins 5 caractères")]
    #[Assert\Regex("/^[a-z][a-z0-9-_]{4,}$/", message:"Doit commencer par une lettre et ne peut contenir que des lettres minuscules, chiffres et -, _ (pas d'espaces)")]
    #[Serializer\Groups(["user:read:collection", "user:read:simplified", "user:update"])]
    protected $userName;

    /**
     * @var int
     * @ORM\Column(type="bigint")
     */
    #[Serializer\Groups(["user:read:owner", "user:update"])]
    #[PhoneNumber]
    protected $phoneNumber;

    /**
     * @var int
     * 1 = Man, 2 = Woman
     * @ORM\Column(type="integer")
     */
    #[Serializer\Groups(["user:read:collection", "user:update"])]
    protected $gender;

    public function getFirstName(): string{
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self{
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string{
        return $this->lastName;
    }

    public function setLastName(string $lastName): self{
        $this->lastName = $lastName;
        return $this;
    }

    public function getUserIdentifier(): string{
        return $this->userName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email):self
    {
        if($email != $this->email){ 
            $this->email = $email;
            $this->emailValidated = false;
        }
        return $this;
    }

    /**
     * Get the value of password
     */ 
    public function getPassword(): ?string
    {
        return $this->password;
    }
    

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Vérifie que le mot de passe de l'utilisateur correspond à celui passé en paramètre
     * @param string $password
     * @return bool
     */
    public function testPassword(string $password){
        return $this->password == self::hashPassword($password);
    }


    /**
     * Get the value of userName
     */ 
    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName(string $userName)
    {
        $this->userName = $userName;
        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(int $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    
    public function getRoles(): array
    {
        return ["ROLE_USER"];
    }
    
    public function getSalt(): ?string{
        return null;
    }
    public function eraseCredentials(){
        
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->userName,
            $this->password,
        ]);
    }

    public function unserialize($data)
    {
        list ($this->id, $this->userName, $this->password) = unserialize($data, ["allowed_classes" => false]);
    }

    public function __toString(){
        return $this->firstName;
    }

    /**
     * Nom de la route pour la page d'accueil de l'utilisateur
     * @return string
     */
    public function getHomePagePath():string{
        return '/';
    }

    /**
     * Get the value of emailValidated
     *
     * @return  bool
     */ 
    public function isEmailValidated()
    {
        return $this->emailValidated;
    }

    public function setEmailValidated(bool $value): self{
        $this->emailValidated = $value;
        return $this;
    }

    public function validateEmail(): self
    {
        $this->emailValidated = true;

        return $this;
    }

    /**
     * 1 = Man, 2 = Woman
     */ 
    public function getGender(): int
    {
        return $this->gender;
    }

    /**
     * Set 1 = Man, 2 = Woman
     *
     * @param  int
     *
     * @return  self
     * @throws \InvalidArgumentException If the gender is unknown
     */ 
    public function setGender(int $gender)
    {
        if(!array_key_exists($gender, self::GENDERS))
            throw new \InvalidArgumentException("The gender '$gender' is unknown");
        $this->gender = $gender;
        return $this;
    }

    /**
     * Get the value of avatarFile
     *
     * @return  ?string
     */ 
    public function getAvatarFile()
    {
        return $this->avatarFile;
    }

    /**
     * Set the value of avatarFile
     *
     * @param  string|null  $avatarFile
     *
     * @return  self
     */ 
    public function setAvatarFile(?string $avatarFile)
    {
        $this->avatarFile = $avatarFile;

        return $this;
    }

    #[Serializer\Groups(["user:read:collection", "user:read:simplified"])]
    public function getAvatarUrl(){
        if($this->avatarFile == null)
            return $this->gender == 1 ? self::DEFAULT_MAN_AVATAR_URL : self::DEFAULT_WOMAN_AVATAR_URL;
        return self::AVATARS_FOLDER . "/" . $this->getAvatarFile();
    }

    public function getLanguage(): string{
        return "fr";
    }

    public static function hashPassword(string $password){
        $passwordHasherFactory = new PasswordHasherFactory([
            self::class => new MessageDigestPasswordHasher('sha512')
        ]);
        $hasher = $passwordHasherFactory->getPasswordHasher(self::class);
        return $hasher->hash($password);
    }

    public function prePersist()
    {
        parent::prePersist();
        $this->password = self::hashPassword($this->password);
    }
}