#
# Setup filesystems
#
# This salt state can be useful in cloud setup, where we have several block
# devices attached to the machines and need to format them.
#
#

{% for fs, fs_details in pillar.get('filesystems', {}).items() %}
create-fs-{{ fs }}:
  cmd.run:
    - name: mkfs -t {{ fs_details.filesystem }} {{ fs_details.disk }}{{ fs_details.partition }}
    - onlyif: test -b {{ fs_details.disk }} && parted {{ fs_details.disk }} print | grep '^ *{{ fs_details.partition }}.*GB' | grep -v '{{ fs_details.filesystem }}'
    - requires:
      - pkg: filesystem-tools

{{ fs_details.mount_point }}:
  file.directory

fstab-for-{{ fs }}:
  file.append:
    - name: /etc/fstab
    - text: {{ fs_details.disk }}{{ fs_details.partition }} {{ fs_details.mount_point }} {{ fs_details.filesystem }} {{ fs_details.mount_options }} 0 1
    - require:
      - file: {{ fs_details.mount_point }}
      - cmd: create-fs-{{ fs }}

mount-fs-{{ fs }}:
  cmd.wait:
    - name: mount {{ fs_details.mount_point }}
    - watch:
      - file: fstab-for-{{ fs }}
    - requires:
      - file: {{ fs_details.mount_point }}
{% endfor %}

{% for path, details in pillar.get('swap', {}).items() %}
init-swap-{{ path }}:
  cmd.run:
    - name: dd if=/dev/zero of={{ path }} bs=1048576 count={{ details.size }} && mkswap {{ path }}
    - unless: test -f {{ path }}

fstab-for-swap-{{ path }}:
  file.append:
    - name: /etc/fstab
    - text: {{ path }} none swap sw 0 0
    - require:
      - cmd: init-swap-{{ path }}

mount-swap-{{ path }}:
  cmd.wait:
    - name: swapon {{ path }}
    - watch:
      - file: fstab-for-swap-{{ path }}
{% endfor %}
