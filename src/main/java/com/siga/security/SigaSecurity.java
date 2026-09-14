package com.siga.security;

import org.springframework.security.core.Authentication;

public final class SigaSecurity {
    private SigaSecurity(){}
    public static SigaUser user(Authentication a){ return (SigaUser)a.getPrincipal(); }
}
