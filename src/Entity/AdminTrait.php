<?php
namespace UserAccountBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Admin entities
 * @author Vivian NKOUANANG (https://github.com/vporel) <dev.vporel@gmail.com>
 */
trait AdminTrait{
    /**
     * Si l'administrateur est super-admin
     * @var bool
     * @ORM\Column(type="boolean") 
     */
    protected $super = false;

    /**
     * Si l'administrateur est au niveau le plus haut (peut créer des super admin)
     * @var bool
     * @ORM\Column(type="boolean") 
     */
    protected $topLevel = false;


    public function getRoles(): array{
        $roles = ["ROLE_ADMIN"];
        if($this->super) $roles[] = "ROLE_ADMIN_SUPER";
        if($this->topLevel) $roles[] = "ROLE_ADMIN_TOP_LEVEL";
        return $roles;
    }

    /**
     * @return bool
     */
    public function isTopLevel(){
        return $this->topLevel;
    }

    /**
     * @return self
     */
    public function setTopLevel($topLevel){
        $this->topLevel = $topLevel;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSuper(){
        return $this->super || $this->isTopLevel();
    }

    /**
     * Set the value of super
     *
     * @return  self
     */ 
    public function setSuper(bool $super)
    {
        $this->super = $super;

        return $this;
    }

    public function getHomePagePath():string{
        return "/admin";
    }

}