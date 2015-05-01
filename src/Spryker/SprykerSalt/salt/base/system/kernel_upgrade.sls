# !!! Dangerous !!!
#
# This state will ensure that latest kernel minor version is installed.
# After new kernel is upgraded, the machine will be rebooted.
# This state is NOT included in highstate and should be executed manually (salt '...' state.sls system.kernel_upgrade)
#
# It's generally good idea to execute it on one host at a time, as it will reboot the machine. So don't use salt '*' - or you will reboot the whole server farm
# and cause website offline for some time!

{%- set version = salt['pillar.get']('kernel:version', '') %}
{%- set repository = salt['pillar.get']('kernel:repository', '') %}

{%- if version != '' %}
linux-image-{{ version }}:
  pkg.latest:
{%- if repository != '' %}
    - fromrepo: {{ repository }}
{%- endif %}

shutdown -r now:
  cmd.wait:
    - watch:
      - pkg: linux-image-{{ version }}

sleep 10s:
  cmd.wait:
    - watch:
      - pkg: linux-image-{{ version }}

{%- endif %}