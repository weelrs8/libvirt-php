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

Obtendo todos os dominios
-------------------------

    <?php

    $app->getDomains();

Ligando um dominio expecifico
-----------------------------

    <?php

    $app->getDomainManager('domain_id')->powerOn();