{%- for environment, environment_details in pillar.environments.items() %}
#
# Directories for tomcat instance
#
/data/shop/{{ environment }}/shared/tomcat:
  file.recurse:
    - source: salt://tomcat/files/tomcat_home
    - user: www-data
    - group: www-data
    - dir_mode: 755
    - file_mode: 755
    - makedirs: true

/data/logs/{{ environment }}/tomcat:
  file.directory:
    - user: www-data
    - group: www-data
    - dir_mode: 755
    - file_mode: 755
    - makedirs: true

/data/shop/{{ environment }}/shared/tomcat/logs:
  file.symlink:
    - target: /data/logs/{{ environment }}/tomcat
    - force: true
    - require:
      - file: /data/logs/{{ environment }}/tomcat
      - file: /data/shop/{{ environment }}/shared/tomcat

/data/shop/{{ environment }}/shared/tomcat/bin:
  file.symlink:
    - target: /usr/share/tomcat7/bin
    - force: true
    - require:
      - file: /data/shop/{{ environment }}/shared/tomcat
      - pkg: tomcat

/data/shop/{{ environment }}/shared/tomcat/lib:
  file.symlink:
    - target: /usr/share/tomcat7/lib
    - force: true
    - require:
      - file: /data/shop/{{ environment }}/shared/tomcat
      - pkg: tomcat

#
# Tomcat config
#
/data/shop/{{ environment }}/shared/tomcat/conf/server.xml:
  file.managed:
    - source: salt://tomcat/files/conf/server.xml
    - template: jinja
    - user: www-data
    - group: www-data
    - mode: 640
    - watch_in:
      - service: tomcat7-{{ environment }}
    - require:
      - file: /data/shop/{{ environment }}/shared/data/common
    - context:
      environment: {{ environment }}
      environment_details: {{ environment_details }}

{% if grains.deployment == 'prod' %}
/data/shop/{{ environment }}/shared/tomcat/newrelic/newrelic.yml:
  file.managed:
    - source: salt://tomcat/files/newrelic/newrelic.yml
    - template: jinja
    - user: www-data
    - group: www-data
    - mode: 640
    - watch_in:
      - service: tomcat7-{{ environment }}
    - context:
      environment: {{ environment }}
      environment_details: {{ environment_details }}
{% endif %}


/etc/default/tomcat7-{{ environment }}:
  file.managed:
    - source: salt://tomcat/files/instance/defaults
    - template: jinja
    - user: root
    - group: root
    - mode: 644
    - watch_in:
      - service: tomcat7-{{ environment }}
    - context:
      environment: {{ environment }}
      environment_details: {{ environment_details }}

/etc/init.d/tomcat7-{{ environment }}:
  file.managed:
    - source: salt://tomcat/files/instance/init
    - template: jinja
    - user: root
    - group: root
    - mode: 755
    - context:
      environment: {{ environment }}
      environment_details: {{ environment_details }}

tomcat7-{{ environment }}:
  service:
    - running
    - enable: True
    - require:
      - file: /data/shop/{{ environment }}/shared/tomcat/logs
      - file: /data/shop/{{ environment }}/shared/tomcat/bin
      - file: /data/shop/{{ environment }}/shared/tomcat/lib
      - file: /data/shop/{{ environment }}/shared/tomcat/conf/server.xml
      - file: /etc/init.d/tomcat7-{{ environment }}
      - file: /etc/default/tomcat7-{{ environment }}

{%- endfor %}

