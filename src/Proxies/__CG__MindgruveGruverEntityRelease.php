<?php

namespace DoctrineProxies\__CG__\Mindgruve\Gruver\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Release extends \Mindgruve\Gruver\Entity\Release implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = [];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', '' . "\0" . 'Mindgruve\\Gruver\\Entity\\Release' . "\0" . 'id', 'tag', 'project', 'service', 'uuid', 'previousRelease', 'nextRelease', 'containerId', 'containerIp', 'containerPort', 'status', 'createdAt', 'modifiedAt'];
        }

        return ['__isInitialized__', '' . "\0" . 'Mindgruve\\Gruver\\Entity\\Release' . "\0" . 'id', 'tag', 'project', 'service', 'uuid', 'previousRelease', 'nextRelease', 'containerId', 'containerIp', 'containerPort', 'status', 'createdAt', 'modifiedAt'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Release $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function getService()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getService', []);

        return parent::getService();
    }

    /**
     * {@inheritDoc}
     */
    public function setService(\Mindgruve\Gruver\Entity\Service $service)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setService', [$service]);

        return parent::setService($service);
    }

    /**
     * {@inheritDoc}
     */
    public function setTag($tag)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTag', [$tag]);

        return parent::setTag($tag);
    }

    /**
     * {@inheritDoc}
     */
    public function getTag()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTag', []);

        return parent::getTag();
    }

    /**
     * {@inheritDoc}
     */
    public function getPreviousRelease()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPreviousRelease', []);

        return parent::getPreviousRelease();
    }

    /**
     * {@inheritDoc}
     */
    public function setPreviousRelease(\Mindgruve\Gruver\Entity\Release $release)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPreviousRelease', [$release]);

        return parent::setPreviousRelease($release);
    }

    /**
     * {@inheritDoc}
     */
    public function getNextRelease()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getNextRelease', []);

        return parent::getNextRelease();
    }

    /**
     * {@inheritDoc}
     */
    public function setNextRelease(\Mindgruve\Gruver\Entity\Release $release)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setNextRelease', [$release]);

        return parent::setNextRelease($release);
    }

    /**
     * {@inheritDoc}
     */
    public function getContainerId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getContainerId', []);

        return parent::getContainerId();
    }

    /**
     * {@inheritDoc}
     */
    public function setContainerId($containerId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setContainerId', [$containerId]);

        return parent::setContainerId($containerId);
    }

    /**
     * {@inheritDoc}
     */
    public function getContainerIp()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getContainerIp', []);

        return parent::getContainerIp();
    }

    /**
     * {@inheritDoc}
     */
    public function setContainerIp($ip)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setContainerIp', [$ip]);

        return parent::setContainerIp($ip);
    }

    /**
     * {@inheritDoc}
     */
    public function getContainerPort()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getContainerPort', []);

        return parent::getContainerPort();
    }

    /**
     * {@inheritDoc}
     */
    public function setContainerPort($port)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setContainerPort', [$port]);

        return parent::setContainerPort($port);
    }

    /**
     * {@inheritDoc}
     */
    public function getProject()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProject', []);

        return parent::getProject();
    }

    /**
     * {@inheritDoc}
     */
    public function setProject(\Mindgruve\Gruver\Entity\Project $project)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProject', [$project]);

        return parent::setProject($project);
    }

    /**
     * {@inheritDoc}
     */
    public function getUuid()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUuid', []);

        return parent::getUuid();
    }

    /**
     * {@inheritDoc}
     */
    public function setUuid($uuid)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUuid', [$uuid]);

        return parent::setUuid($uuid);
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($status)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStatus', [$status]);

        return parent::setStatus($status);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatus', []);

        return parent::getStatus();
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusOptions()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatusOptions', []);

        return parent::getStatusOptions();
    }

    /**
     * {@inheritDoc}
     */
    public function getModifiedAt()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getModifiedAt', []);

        return parent::getModifiedAt();
    }

    /**
     * {@inheritDoc}
     */
    public function setModifiedAt(\DateTime $dateTime)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setModifiedAt', [$dateTime]);

        return parent::setModifiedAt($dateTime);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedAt()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCreatedAt', []);

        return parent::getCreatedAt();
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt(\DateTime $dateTime)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCreatedAt', [$dateTime]);

        return parent::setCreatedAt($dateTime);
    }

    /**
     * {@inheritDoc}
     */
    public function updateTimestamps()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'updateTimestamps', []);

        return parent::updateTimestamps();
    }

}
