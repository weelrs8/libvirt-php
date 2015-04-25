Libvirt PHP
===========

Conectando na Plataforma
------------------------

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

Criando um novo Domínio
-----------------------

    <?php

    $app->newDomain(
        100, // ID do Domínio
        1,   // VCPU = 1
        1,   // Memória = 1GB
        [
            'size' => 10,   // HD = 10GB
            'path' => ini_get('libvirt.image_path')     // Diretório onde ficará salvo a imagem
        ],
        'bridge:br0',   // Rede
        [
            'path' => 'http://172.17.10.180/pub/centos/6.6/os/x86_64',   // PXE
            'ks'   => 'http://172.17.10.180/pub/centos/6.6/ks.cfg'   // Kickstart
        ],
        [
            'type' => 'linux',   // linux, windows
            'variant' => 'rhel6'    // rhel6, win7
        ]
    );


Exibindo todos os Dominios
--------------------------

    <?php

    $app->getDomains();

Gerenciando um Domínio Específico
---------------------------------

    <?php

    $app->getDomainManager('domain_id')->powerOn();