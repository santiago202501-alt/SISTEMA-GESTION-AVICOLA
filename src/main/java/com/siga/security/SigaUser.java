package com.siga.security;

import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.userdetails.User;
import java.util.Collection;

public class SigaUser extends User {
    private final long id;
    private final Long clienteId;
    private final String nombreCompleto;
    private final String rol;

    public SigaUser(long id, Long clienteId, String nombreCompleto, String rol, String username, String password,
                    Collection<? extends GrantedAuthority> authorities) {
        super(username, password, true, true, true, true, authorities);
        this.id = id;
        this.clienteId = clienteId;
        this.nombreCompleto = nombreCompleto;
        this.rol = rol;
    }
    public long getId(){ return id; }
    public Long getClienteId(){ return clienteId; }
    public String getNombreCompleto(){ return nombreCompleto; }
    public String getRol(){ return rol; }
}
