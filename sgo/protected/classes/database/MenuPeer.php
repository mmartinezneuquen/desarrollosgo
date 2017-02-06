<?php
class MenuPeer
{
	
	public static function MenuByUsuario($id){
		$where = "WHERE X.IDUSUARIO='$id' OR X.IDUSUARIO IS NULL AND ((X.IDPAGINA IS NOT NULL AND X.CONTENIDOS>0) OR (X.CONTENIDOS>0))";

		$sql = "select
  					*
				from
			  		(select
			            IdMenu as IDMENU,
			      		null as IDPAGINA,
			            Nombre as NOMBRE,
			            Orden is null as PIVOT,
			            Orden as ORDEN,
			            null as PAGINA,
			         	null as CONTENEDOR,
			         	null as IDCONTENEDOR,
			         	null as IDUSUARIO,
			         	null as USERNAME,
			            (select 
			            		count(IdMenu) 
			            	from 
			            		menu 
			            		inner join rolpagina on menu.IdPagina = rolpagina.IdPagina 
			            		inner join usuario_rol on rolpagina.IdRol = usuario_rol.IdRol
			            		inner join pagina on rolpagina.IdPagina = pagina.IdPagina 
			            	where
			            		menu.IdMenuContenedor = m.IdMenu and usuario_rol.IdUsuario='$id' 
			            		and menu.Activo=1 and pagina.Activa=1
			            ) +
			            (select 
			            		count(mh.IdMenu) 
			            	from 
			            		menu mp 
			            		inner join menu mh on mp.IdMenu = mh.IdMenuContenedor 
			            		inner join rolpagina on mh.IdPagina = rolpagina.IdPagina 
			            		inner join usuario_rol on rolpagina.IdRol = usuario_rol.IdRol
			            		inner join pagina on rolpagina.IdPagina=pagina.IdPagina 
			            	where 
			            		mp.IdMenuContenedor = m.IdMenu and usuario_rol.IdUsuario='$id' 
			            		and pagina.Activa=1 and mp.Activo=1 and mh.Activo=1
			            ) AS CONTENIDOS,
			         	Target as TARGET
			        from
			        	menu m
			       	where
			       		Activo = 1 and
			       		IdMenuContenedor is null and
			       		IdPagina is null
					union
					SELECT
						m.IdMenu as IDMENU,
						p.IdPagina as IDPAGINA,
						m.Nombre as NOMBRE,
						m.Orden is null as PIVOT,
						m.Orden as ORDEN,
						p.Pagina as PAGINA,
						mc.Nombre AS CONTENEDOR,
						mc.IdMenu AS IDCONTENEDOR,
						usuario.IdUsuario as IDUSUARIO,
						usuario.Username AS USERNAME,
						(select 
								count(IdMenu) 
							from 
								menu 
								inner join rolpagina on menu.IdPagina = rolpagina.IdPagina 
								inner join usuario_rol on rolpagina.IdRol = usuario_rol.IdRol
								inner join usuario on usuario_rol.IdUsuario = usuario.IdUsuario 
								inner join pagina on rolpagina.IdPagina=pagina.IdPagina 
							where 
								menu.IdMenuContenedor = m.IdMenu
								and usuario.IdUsuario='$id' 
								and pagina.Activa=1 and menu.Activo=1
						) +
						(select 
								count(mh.IdMenu) 
							from 
								menu mp 
								inner join menu mh on mp.IdMenu = mh.IdMenuContenedor 
								inner join rolpagina on mh.IdPagina = rolpagina.IdPagina 
								inner join pagina on rolpagina.IdPagina=pagina.IdPagina 
							where
								mp.IdMenuContenedor = m.IdMenu 
								and usuario.IdUsuario='$id' 
								and pagina.Activa=1 
								and mp.Activo=1 
								and mh.Activo=1
						) as CONTENIDOS,
						m.Target as TARGET
					from
						menu m 
						left join pagina p on m.IdPagina = p.IdPagina 
						left join menu mc on m.IdMenuContenedor = mc.IdMenu 
						left join rolpagina r on p.IdPagina = r.IdPagina 
						left join usuario_rol ur on r.IdRol = ur.IdRol
						left join usuario on ur.IdUsuario = usuario.IdUsuario
					where
						usuario.IdUsuario='$id' and
						m.Activo=1
			) X
			$where
			order by
				PIVOT ASC,
				ORDEN ASC,
				X.NOMBRE ASC";

		return $sql;
	}

}
?>