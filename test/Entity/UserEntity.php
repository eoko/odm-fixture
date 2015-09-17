<?php
namespace Eoko\ODM\Fixture\Test\Entity;

use Zend\Stdlib\ArraySerializableInterface;

use Eoko\ODM\Metadata\Annotation\Document;
use Eoko\ODM\Metadata\Annotation\StringField;
use Eoko\ODM\Metadata\Annotation\Number;
use Eoko\ODM\Metadata\Annotation\DateTime;
use Eoko\ODM\Metadata\Annotation\KeySchema;
use Eoko\ODM\Metadata\Annotation\Boolean;
use Zend\Stdlib\Hydrator\ClassMethods;


/**
 * @Document(table="oauth_users", provision={"ReadCapacityUnits" : 1, "WriteCapacityUnits" : 1})
 * @KeySchema(keys={"username" : "HASH"})
 */
class UserEntity
{

    /**
     * @StringField
     */
    protected $username;

    /**
     * @StringField
     */
    protected $display_name;

    /**
     * @StringField
     */
    protected $given_name;

    /**
     * @StringField
     */
    protected $family_name;

    /**
     * @StringField
     */
    protected $address;

    /**
     * @DateTime
     * @var  \DateTime
     */
    protected $created_at;

    /**
     * @StringField
     */
    protected $created_by;

    /**
     * @StringField
     */
    protected $source;

    /**
     * @StringField
     */
    protected $email;

    /**
     * @StringField
     */
    protected $password;

    /**
     * @Boolean
     */
    protected $email_verified = false;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmailVerified()
    {
        return $this->email_verified;
    }


    /**
     * @param mixed $email_verified
     */
    public function setEmailVerified($email_verified)
    {
        $this->email_verified = $email_verified;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }



    /**
     * @return mixed
     */
    public function getCreatedAt($format = null)
    {
        if(is_string($format) && $this->created_at instanceof \DateTime) {
            return $this->created_at->format($format);
        }
        return $this->created_at;
    }

    /**
     * @param $created_at
     */
    public function setCreatedAt($created_at = null)
    {
        $this->created_at = ($created_at) ? $created_at : new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        $displayName = [];
        if($this->display_name) {
            $displayName[] = $this->display_name;
        }

        if(!$displayName) {
            if($this->family_name) {
                $displayName[] = $this->family_name;
            }

            if($this->given_name) {
                $displayName[] = $this->given_name;
            }
        }

        if(!$displayName) {
            $displayName[] = $this->getUsername();
        }


        return implode(' ', $displayName);
    }

    /**
     * @param mixed $display_name
     */
    public function setDisplayName($display_name)
    {
        $this->display_name = $display_name;
    }

    /**
     * @return mixed
     */
    public function getGivenName()
    {
        return $this->given_name;
    }

    /**
     * @param mixed $given_name
     */
    public function setGivenName($given_name)
    {
        $this->given_name = $given_name;
    }

    /**
     * @return mixed
     */
    public function getFamilyName()
    {
        return strtoupper($this->family_name);
    }
    /**
     * @param mixed $family_name
     */
    public function setFamilyName($family_name)
    {
        $this->family_name = $family_name;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy($created_by)
    {
        $this->created_by = $created_by;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }


}