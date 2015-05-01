# This state is not included in highstate - it's executed manually during master setup
# Note - pillars are not available here
#
# Command to run it:
# salt-call -l error state.sls system.master

include:
  - .repositories
  - .utils
  - .sudoers
  - .vim
  - .time
  - .firewall
  - .sysctl
  - .rackspace

mobuchowicz-root:
  ssh_auth:
    - present
    - user: root
    - name: ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEAu6B/194wiz1Phd1tGGnlPJaFwHUm+Fyc2Ku8mMuPIodwqLjTa+ZZ3lhOmxHgO2VTeC/46p7HprlatSWBiS3rm28HW3tM0wLyxazUNN5xmUjRuYRun7IGlo9Q9BvBMdgNTZ464DWPbidRqHFoYG6Qh8+Tt2orEc/YcwKLzkjcvRYWuFRsf0yQr25Ouoweq+hXEetYPn67yWNndqfzBOvPDAYKcLy2rvnLNlE0GSlD52dLJ3uPFLa7IGlg9uI0wW9shyeLy04P+197rqRoMkeMHRrvgBIud3Z8Xz0nOxEivD+nFXnpaV4wHxEPaViWhuFXvRrsSltDU7+jGyrJbV5GpQ== marek.obuchowicz@project-a.com

mkugele-root:
  ssh_auth:
    - present
    - user: root
    - name: ssh-dss AAAAB3NzaC1kc3MAAACBANlqvMwT+YjXpHrD9OrOV5fYdf22YPogvpumazCsv9gd9653BbNxOh+Pw5COJw6g4PYWt8BQp20CmuJ1y/xLkBxZLalW33o4b+UySI5JMSHBHvkHYpmBZJaw3dr++R8LmjNubkJQCOAPd76UoOOH4LTce2au9/uNNo5/5CGgiYH3AAAAFQCmroK7fgBCFVkl8ZmwYLH5FFmNSwAAAIBOWpPsW7dr4a8RE3M07K/R/txdN8Fn8dga8DtTidvHzTVXw0iT2Im/71aBcSNniigCfIez6TnCx/aiaXVAFno51GJ0PRAxeMgg0B184LvaWnMpTFh4GBntL49OqZ1Suu80hOdzOvhci6oQj7eS3kBTk7ia6+r41Fggva9oriR1yAAAAIAR5cnYYmVQXLQsZ7LXs+7+GMvHTb2WQsidcq2RuFG2Q+8drqAZj6DnLXvGXUYpsf866AZcyQPdNRsNV2/xajO8rd3T7wWGjpY5iq/vpa9kpiS1O9LEbrPUqasxyKS7KXAKc888T0VA+3Ogf3MTSrQBXvBa4kbYOoQk0Y7LsQcCtA== michael.kugele@project-a.com

dseif-root:
  ssh_auth:
    - present
    - user: root
    - name: ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC7vEFL5GyaLz7SaEJheLVAiJg9cnY9td2VAjk1EGBuIu/qZhR5Io1Lf1HbKsGtIC6M7dbs/4ZTsgopHv3svcO9wDs14Q9CKUBMFf6y/kv7e/D2uqBC+thAcGtRfr/rZjjMZslRxqNJT9+pMSrnoLKYuFlW1fW9yjrUmHgwCSFUtsLiJIAZs/Rp0L7Psy/RfQVlqjbUUMfC+eDXDk7U58oVADtur19m2BoRpr6O1wtUmu90xN3A56K9iGf9UBLa0NAX5cp+lbuM4jklkmxOMvjiZZVmqAntzVdSSqt+wh/aD35KJJvinjtwjPc/tX37nLz16pqEo2DuiavTuJY8btB7 daniel.seif@project-a.com


fwesner-root:
  ssh_auth:
    - present
    - user: root
    - name: ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQClukGZpIrY3sQM2n/alRzuYoaUwiomE1//7UWDQNNQQQh7dw0q28YtY3wkcjvep1Jq8gS6zBwgGIQO0EOMyoF5wgodg0D4kBVvkjxGBw92Ia+KMhFNo0LHc1rz2Qh38RAGKzP4dolbGQ1HeyC8KcKe0cuzO/gk36v5/2JPchqovAZLYYJytW4tLvPHm3r4x+OMytwYxxCPf4ohtjuOOKUuoFf19V/v9z0RAphZwPuPYWskiHbDx+nM/biKHQIQEa0oYKyprGM+YEUEam4BSJ8jdoF9amvyLSaSl06Aic6HN5fiARbcU2jVqa55qL/Aw7f9FEKh0T60Ec5ZZvlGH0T5 fabian.wesner@project-a.com

marcorossdeutscher-root:
  ssh_auth:
    - present
    - user: root
    - name: ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDQwkNp9aTlUOjWIrb9Lc6ewj63gPM1o/WWi/nk0f8ZflEb/CKuwf3NedELLZry5bw225JISAMcBzyPUEB1RasPSJ926ezaNgN/8N1OUYAXNvhmEIsm8BIVdV4idJFqf9Y9gaByItNbQ+ZUJzIHN+7hKsZLQABMmOKc4g+bA5ZWHfAk4yJaoNbkgG12Iq+g3KQLWXZo9M7xsdC2c0sHNjSQKvEiRLIDHvKOw5Wrh8o8ObgtYMQQeYTTQ4vGeQINW4woJXtR6XUkl7rK6+NV2qqSwU64zGAnwuhI2P8oZxDsrEiHqRy6VjYtXm6gcZ8KoUmhzUMBdhWf2eULBUXXhBfj marcorossdeutscher@MacBook-Project-A.local

{% if pillar.server_env.ssh.id_rsa is defined %}
/root/.ssh/id_rsa:
  file.managed:
    - user: root
    - group: root
    - mode: 400
    - contents_pillar: server_env:ssh:id_rsa
{% endif %}