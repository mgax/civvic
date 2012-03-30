require_recipe "apt"
require_recipe "apache2::mod_php5"


#[''].each do |pkg|
#  package pkg do
#    :upgrade
#  end
#end


apache_site "default" do
  enable false
end
