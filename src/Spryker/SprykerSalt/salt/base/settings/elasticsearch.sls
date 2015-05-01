{%- from 'settings/hosts.sls' import hosts with context %}

{%- set total_nodes = (hosts.elasticsearch_data)|count %}
{%- set minimum_nodes = ( total_nodes / 2 )|round|int %}

{%- if total_nodes > 1 %}
{%-   set replicas = 1 %}
{%-   set shards = 6 %}
{%- else %}
{%-   set replicas = 0 %}
{%-   set shards = 1 %}
{%- endif %}



{%- set elasticsearch = {} %}
{%- do elasticsearch.update ({
  'minimum_nodes'        : minimum_nodes,
  'total_nodes'          : total_nodes,
  'shards'               : shards,
  'replicas'             : replicas,
}) %}
