#!/usr/bin/env bash

# Show version
show_version()
{
    echo "Cs-cart toolbox (alpha)"
}

show_help()
{
	echo "Usage: $SCRIPT_NAME [--debug] [--verbose] COMMAND arg...

A utility tool to setup DCS projects

Commands:
    env:up          Run environment
    env:logs        Show logs
    env:php-logs    Show PHP logs
    version         Show version


Deprecated:
	build       Build the application
	connect     Shortcut to connect to app container
	get         Set up local environment
	help        Display this menu
	hosts       Display helper for host entry
	log         Display app error logs
	rebuild     Rebuild containers
	restore-env Restore default environments
	reload      Reload containers
	self-update Update the version
	setup       Installs application
	setup-mail  Setup SMTP mailing in an application
	stat		Get statistics out of the repository
	test        Run test suite

Flags:
	--verbose   Does not mask output details
	--debug     Displays every command being run. Verbose ++.

Run '$SCRIPT_NAME COMMAND --help' for more information on a command."
}

##
# Load a BASH library
# (stored in the /bin folder of the "ecom-full" application, unless specified otherwise)
#
# @example bi_load_lib corefunc
#
# @uses APP_DIR
# @sets APP_LIB_DIR
##
function bi_load_lib
{
	# The application's bin directory
	APP_LIB_DIR=${APP_LIB_DIR:-"$APP_DIRECTORY/toolbox-lib"}

	if test -z $1 || ! test -f "$APP_LIB_DIR/$1" ; then
		printf "Library '$1' could not be loaded.\n" >&2
		exit 1;
	fi

	. "$APP_LIB_DIR/$1"
}

#######################
# Application execution
#######################

############
# Basic init
############

# Abort on errors
trap 'exit' ERR
set -o pipefail

# The script name
SCRIPT_NAME=$0

#CSCART_DIRECTORY="$( cd ".." && pwd )"
echo "DEBUG MODE "
CSCART_DIRECTORY=$(pwd)

if ! test -f "$CSCART_DIRECTORY/init.php" ; then
    printf >&2 "CS-Cart application could not be found in repository '$CSCART_DIRECTORY'.\n"
    exit 1
fi

# The application directory
APP_DIRECTORY=$(command -v perl > /dev/null && dirname "$(perl -e 'use Cwd "abs_path";print abs_path(shift)' $0)" || dirname "$(readlink -f "${BASH_SOURCE[0]}")")

# Load core libraries
bi_load_lib core
bi_load_lib docker
bi_load_lib docker-app

bi_load_lib JSON.sh
bi_load_lib json

if test $# = 0 ; then
	show_help
	exit 1
fi

STOP=

###########################
# Pre-bootstrapped commands
###########################

while test $# != 0 && test -z "$STOP"
do
	case "$1" in
		-h|--help|h|help)
			show_help
			exit
			;;

		v|version|-v|--version)
			show_version
			show_help
			exit
			;;

		--verbose)
			VERBOSE=y
			shift
			;;

		--debug)
			DEBUG=y
			shift
			;;

		*)
			STOP=y
			;;
	esac
done


########################
# Bootstrapping commands
########################

load_configuration

#######################
# Bootstrapped commands
#######################

while test $# != 0
do
	case "$1" in
	    env:up)
	        shift
	        docker_compose up "$@"
	        ;;

	    env:logs)
	        shift
	        docker_compose logs "$@"
	        ;;

	    env:php-logs)
	        shift
	        docker_compose exec php tail /var/log/php-fpm/error.log "$@"
	        ;;

	    app:setup)
	        ;;

		build)
			shift
			build_repository "$@"
			exit
			;;

	    get)
			shift
			bi_load_lib get-repository
			get_repository "$@"
			exit
			;;

		hosts)
			show_hosts
			exit
			;;

		log)
			shift
			log_app "$@"
			exit
			;;

		connect)
			shift
			connect_app "$@"
			exit
			;;

		rebuild)
			shift
			build_containers "$@"
			exit
			;;

		reload)
			shift
			do_reload
			exit
			;;

		restore-env)
			bi_load_lib setup
			restore_env
			exit
			;;

		test)
			shift
			run_tests "$@"
			exit
			;;

		self-update)
			bi_load_lib setup
			do_self_update
			exit
			;;

		*)
	    	show_help
	    	exit 1
			;;
	esac
	shift
done
