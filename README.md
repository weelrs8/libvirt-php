Libvirt PHP
===========

Conectando KVM TCP
------------------

    <?php
    use LibvirtPHP\App\Libvirt;
    use LibvirtPHP\Auth\QemuTcp;

    $app = new Libvirt();
    $app->connect(
        new QemuTcp(
            'example.com',
            [
                'username' => 'user',
                'password' => 'pass'
            ]
        )
    );

Construindo um Dominio
----------------------

    <?php

    $app->newDomain(
        100,
        1,
        1,
        [
            'size' => 10,
            'path' => ini_get('libvirt.image_path')
        ],
        'bridge:br0',
        [
            'path' => 'http://172.17.10.180/centos/6.6/os/x86_64',
            'ks'   => 'http://172.17.10.180/centos/6.6/ks.cfg'
        ],
        [
            'type' => 'linux',
            'variant' => 'rhel6'
        ]
    );


Obtendo todos os dominios
-------------------------

    <?php

    $app->getDomains();

Ligando um dominio expecifico
-----------------------------

    <?php

    $app->getDomainManager('domain_id')->powerOn();