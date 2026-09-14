package com.siga.security;

import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;

@Service
public class SigaUserDetailsService implements UserDetailsService {

    private final JdbcTemplate jdbc;

    public SigaUserDetailsService(JdbcTemplate jdbc) {
        this.jdbc = jdbc;
    }

    @Override
    public UserDetails loadUserByUsername(String email) throws UsernameNotFoundException {

        String sqlUsuario =
                "SELECT u.id, u.nombre_completo, u.email, u.password_hash, " +
                "u.activo, u.cliente_id, r.nombre AS rol " +
                "FROM usuarios u " +
                "JOIN roles r ON r.id = u.rol_id " +
                "WHERE u.email = ? " +
                "LIMIT 1";

        List<UsuarioData> usuarios = jdbc.query(
                sqlUsuario,
                ps -> ps.setString(1, email),
                (rs, rowNum) -> new UsuarioData(
                        rs.getLong("id"),
                        rs.getString("nombre_completo"),
                        rs.getString("email"),
                        rs.getString("password_hash"),
                        rs.getBoolean("activo"),
                        rs.getObject("cliente_id") == null
                                ? null
                                : rs.getLong("cliente_id"),
                        rs.getString("rol")
                )
        );

        if (usuarios.isEmpty()) {
            throw new UsernameNotFoundException("Usuario no encontrado");
        }

        UsuarioData usuario = usuarios.get(0);

        if (!usuario.activo()) {
            throw new UsernameNotFoundException("Usuario inactivo");
        }

        List<SimpleGrantedAuthority> authorities = new ArrayList<>();

        String sqlPermisos =
                "SELECT p.nombre " +
                "FROM permisos p " +
                "JOIN rol_permisos rp ON rp.permiso_id = p.id " +
                "JOIN roles r ON r.id = rp.rol_id " +
                "WHERE rp.rol_id = ?";

        jdbc.query(
                sqlPermisos,
                ps -> ps.setLong(1, obtenerRolId(email)),
                rs -> {
                    while (rs.next()) {
                        authorities.add(
                                new SimpleGrantedAuthority(rs.getString("nombre"))
                        );
                    }
                }
        );

        authorities.add(
                new SimpleGrantedAuthority(
                        "ROLE_" + usuario.rol().toUpperCase()
                )
        );

        return new SigaUser(
                usuario.id(),
                usuario.clienteId(),
                usuario.nombreCompleto(),
                usuario.rol(),
                usuario.email(),
                usuario.passwordHash(),
                authorities
        );
    }

    private long obtenerRolId(String email) {

        String sql =
                "SELECT rol_id " +
                "FROM usuarios " +
                "WHERE email = ? " +
                "LIMIT 1";

        Long rolId = jdbc.queryForObject(
                sql,
                Long.class,
                email
        );

        if (rolId == null) {
            throw new UsernameNotFoundException("Rol no encontrado");
        }

        return rolId;
    }

    private record UsuarioData(
            long id,
            String nombreCompleto,
            String email,
            String passwordHash,
            boolean activo,
            Long clienteId,
            String rol
    ) {
    }
}