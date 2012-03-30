# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant::Config.run do |config|
  config.vm.box = "lucid32"
  config.vm.network :hostonly, "192.168.84.95"

  config.vm.provision :chef_solo do |chef|
    chef.add_recipe "civvic"
  end
end
