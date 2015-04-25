<?php

namespace LibvirtPHP\App;

use LibvirtPHP\Auth\InterfaceAuth;
use LibvirtPHP\Manager\Domain;
use SSH\Auth\Password;
use SSH\SSHConnection;
use SSH\SSHAuthentication;

class Libvirt
{
    private $resource;

    /**
     * Connect
     *
     * @param InterfaceAuth $auth
     */
    public function connect(InterfaceAuth $auth)
    {
        $this->resource = $auth->authenticate();
    }

    /**
     * Is Connected
     *
     * @return bool
     */
    public function isConnected()
    {
        return (is_resource($this->resource) ? true : false);
    }

    /**
     * Domain Manager
     *
     * @param $domain
     * @return Domain
     * @throws \Exception
     */
    public function getDomainManager($domain)
    {
        if (!is_resource($domain))
            $domain = $this->getDomainResource($domain);

        return new Domain($domain);
    }

    /**
     * Domain Resource
     *
     * @param $domain
     * @return resource
     * @throws \Exception
     */
    public function getDomainResource($domain)
    {
        $domain = libvirt_domain_lookup_by_name($this->resource, $domain);
        if(is_resource($domain))
            return $domain;

        $domain = libvirt_domain_lookup_by_uuid_string($this->resource, $domain);
        if(is_resource($domain))
            return $domain;

        throw new \Exception("Dominio informado não existe: " . libvirt_get_last_error());
    }

    /**
     * Get Domains
     *
     * @return array
     */
    public function getDomains()
    {
        return libvirt_list_domains($this->resource);
    }

    /**
     * Get Version
     *
     * @return array
     */
    public function getVersion()
    {
        return libvirt_version();
    }

    public function newDomain($id, $vcpu, $memory, array $disk, $network, array $path, array $os)
    {
        $id = sprintf("vm-%u", $id);
        $vcpu = (int) $vcpu;
        $memory = (int) (1024 * (int) $memory);
        $disk['size'] = (int) $disk['size'];

        /*$cmd = <<<EOF
            nohup virt-install \
            -n $id -r $memory --vcpus=$vcpu --disk path={$disk['path']}/$id,size={$disk['size']} \
            --network $network --location={$path['path']} --extra-args "ks={$path['ks']}" \
            --graphics vnc,listen=0.0.0.0 --noautoconsole \
            --os-type={$os['type']} --os-variant={$os['variant']} --wait=-1 > /logs/$id.log &
EOF;*/

        $cmd =
            sprintf(
                'nohup virt-install \
                 --name %s --ram %u --vcpus=%u --disk path=%s,size=%d \
                 --network %s --location=%s --extra-args "ks=%s" \
                 --graphics vnc,listen=0.0.0.0 --noautoconsole \
                 --os-type=%s --os-variant=%s --wait=-1 > %s &
                ',
                $id,
                $memory,
                $vcpu,
                $disk['path'].'/'.$id,
                $disk['size'],
                $network,
                $path['path'],
                $path['ks'],
                $os['type'],
                $os['variant'],
                "/logs/{$id}.log"
            )
        ;

        return $this->ssh_run($cmd);
    }

    private function ssh_run($cmd)
    {
        $ssh = new SSHConnection();
        $ssh->open('127.0.0.1');
        $ssh->authenticate(
            new Password(
                'root',
                '123456'
            )
        );

        return $ssh->run($cmd);
    }
}