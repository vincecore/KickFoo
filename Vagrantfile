# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
      config.vm.box = "ubuntu/trusty64"

      config.vm.provider :virtualbox do |v|
          v.name = "kickfoo"
          v.customize [
              "modifyvm", :id,
              "--name", "kickfoo",
              "--memory", 1024,
              "--natdnshostresolver1", "on",
              "--cpus", 1,
          ]
      end

      config.vm.network :private_network, ip: "192.168.66.15"
      config.ssh.forward_agent = true

      config.vm.synced_folder "./", "/vagrant", type: "nfs", :mount_options => ['nolock,vers=3,udp,noatime,actimeo=1']

      config.vm.provision "fix-no-tty", type: "shell" do |s|
            s.privileged = false
            s.inline = "sudo sed -i '/tty/!s/mesg n/tty -s \\&\\& mesg n/' /root/.profile"
      end

      config.vm.provision "shell",
           inline: "/vagrant/provisioning/provisioning.sh"

      config.vm.provision "ansible" do |ansible|
          ansible.verbose = "vv"
          ansible.playbook = "provisioning/playbook.yml"
          ansible.inventory_path = "provisioning/inventories/dev"
          ansible.limit = 'all'
          ansible.extra_vars = {
              private_interface: "192.168.66.15",
              hostname: "kickfoo"
          }
      end
end
