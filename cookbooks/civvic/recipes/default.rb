require_recipe "apt"
require_recipe "apache2::mod_php5"


['php5-cli', 'php5-curl', 'php5-mysql', 'smarty'].each do |pkg|
  package pkg do
    :upgrade
  end
end


apache_site "default" do
  enable false
end


web_app "civvic" do
  template "civvic.conf.erb"
  docroot "#{@node[:vagrant][:directory]}/www"
end
