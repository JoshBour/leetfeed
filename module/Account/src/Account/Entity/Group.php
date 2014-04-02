<?php
namespace Account\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Group
 * @package Account\Entity
 * @ORM\Entity
 * @ORM\Table(name="groups")
 */
class Group
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     * @ORM\Column(name="group_id")
     */
    private $groupId;

    /**
     * @ORM\OneToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="group_parent", referencedColumnName="group_id")
     * @ORM\Column(nullable=true)
     * @ORM\Column(name="group_parent")
     */
    private $groupParent;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Account\Entity\Account", inversedBy="groups")
     */
    private $accounts;

//    /**
//     * @ORM\OneToMany(targetEntity="PagePermission", mappedBy="group")
//     */
//    private $permittedPages;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
    #    $this->permittedPages = new ArrayCollection();
    }

    /**
     * Sets the corresponding accounts to the group.
     *
     * @param Account $accounts
     * @return Group
     */
    public function setAccounts($accounts)
    {
        $this->accounts[] = $accounts;
        return $this;
    }

    /**
     * Gets the corresponding accounts to the group.
     *
     * @return Account
     */
    public function getAccounts()
    {
        return $this->accounts;
    }

    /**
     * Sets the group's unique id.
     *
     * @param int $groupId
     * @return Group
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * Gets the group's unique id.
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Sets the group's parent.
     *
     * @param $groupParent
     * @return Group
     */
    public function setGroupParent($groupParent){
        $this->groupParent = $groupParent;
        return $this;
    }

    /**
     * Gets the groups parent.
     *
     * @return null|Group
     */
    public function getGroupParent(){
        return $this->groupParent;
    }

    /**
     * Sets the group's name.
     *
     * @param string $name
     * @return Group
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Gets the group's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

//    /**
//     * Sets the permitted pages for this group.
//     *
//     * @param ArrayCollection $permittedPages
//     * @return Group
//     */
//    public function setPermittedPages($permittedPages){
//        $this->permittedPages[] = $permittedPages;
//        return $this;
//    }
//
//    /**
//     * Gets the permitted pages for this group.
//     *
//     * @return ArrayCollection
//     */
//    public function getPermittedPages(){
//        return $this->permittedPages;
//    }
//
//    /**
//     * Adds permitted pages to the group's current ones.
//     *
//     * @param Array|PagePermission $permittedPages
//     */
//    public function addPermittedPages($permittedPages){
//        if(is_array($permittedPages)){
//            foreach($permittedPages as $permittedPage){
//                $this->permittedPages->add($permittedPage);
//            }
//        }else{
//            $this->permittedPages->add($permittedPages);
//        }
//    }
//
//    /**
//     * Removes permitted pages from the group's current ones.
//     *
//     * @param Array|PagePermission $permittedPages
//     */
//    public function removePermittedPages($permittedPages){
//        if(is_array($permittedPages)){
//            foreach($permittedPages as $permittedPage){
//                $this->permittedPages->removeElement($permittedPage);
//            }
//        }else{
//            $this->permittedPages->removeElement($permittedPages);
//        }
//    }

}
