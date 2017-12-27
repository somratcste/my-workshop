-- self join and search

Select w.name_en as source_name, wh.name_en as destination_name from shipments s
left join wire_houses w on
w.id = s.source_id
left join wire_houses wh on
wh.id = s.destination_id
where s.status < 2 and w.name_en like 'd%'

======================