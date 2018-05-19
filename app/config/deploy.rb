# Server
set :application,           "Scantrad Alert"
set :user,                  "jerem"
set :use_set_permissions,   true
set :permission_method,     :acl
set :use_sudo,              false
set :webserver_user,        "www-data"
set :use_set_permission,    true
default_run_options[:pty] = true
ssh_options[:forward_agent] = true
set :deploy_via,  :copy
set :use_composer,  true
set :domain, "5.196.69.196"
set :permission_method, :acl

role :web,        domain
role :app,        domain, :primary => true
role :db,         domain, :primary => true

# Multistaging
set :stage_dir,             "app/config/deploy"
set :stages do
   names = []
   for filename in Dir["#{stage_dir}/*.rb"]
       names << File.basename(filename, ".rb")
   end
   names
end
set :default_stage,         "prod"
require 'capistrano/ext/multistage'
set :stage_files,           false

# Repository
set :repository,            "git@github.com:JeremieSamson/scantrad_manga_alert.git"
set :scm,                   :git
set :deploy_via,            :remote_cache
set :keep_releases,         3

# Symfony
set :parameters_file,       false
set :assets_symlinks,       true
set :app_path,              "app"
set :var_path,              "var"
set :model_manager,         "doctrine"
set :interactive_mode,      false
set :dump_assetic_assets,   false
set :shared_files,          ["#{app_path}/config/parameters.yml"]
set :writable_dirs,         ["#{var_path}/logs", "#{var_path}/cache", "#{var_path}/sessions"]
set :shared_children,       ["#{var_path}/logs"]

logger.level = Logger::MAX_LEVEL

# Backup remote database to local
before "deploy:rollback:revision", "database:dump:remote"

# Update Database
after "symfony:cache:warmup", "symfony:doctrine:schema:update"

# Run deployment
after "deploy", "deploy:cleanup" # Clean old releases at the end

