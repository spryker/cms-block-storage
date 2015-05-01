{%- for environment, environment_details in pillar.environments.items() %}

/data/shop/{{ environment }}/shared/data/common/jenkins:
  file.directory:
    - mode: 755
    - user: www-data
    - group: www-data
    - makedirs: True

/data/shop/{{ environment }}/shared/data/common/jenkins/config.xml:
  file.managed:
    - mode: 644
    - user: www-data
    - group: www-data
    - source: salt://tomcat/files/jenkins/config.xml
    - replace: False
    - template: jinja
    - context:
      environment: {{ environment }}
    - require:
      - file: /data/shop/{{ environment }}/shared/data/common/jenkins
    - watch_in:
      - service: tomcat7-{{ environment }}

/data/shop/{{ environment }}/shared/tomcat/conf/Catalina/localhost/jenkins.xml:
  file.managed:
    - mode: 644
    - user: www-data
    - group: www-data
    - source: salt://tomcat/files/jenkins/context.xml
    - template: jinja
    - context:
      environment: {{ environment }}

/data/shop/{{ environment }}/shared/tomcat/webapps/jenkins.war:
  file.copy:
    - source: /data/deploy/download/jenkins-{{ pillar.jenkins.version }}.war
    - require:
      - cmd: download-jenkins.war
      - file: /data/shop/{{ environment }}/shared/tomcat/conf/Catalina/localhost/jenkins.xml
    - watch_in:
      - service: tomcat7-{{ environment }}

{%- endfor %}