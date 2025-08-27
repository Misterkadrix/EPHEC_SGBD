export interface Deplacement {
    id: number;
    session_depart_id: number;
    site_depart_id: number;
    room_depart_id: number;
    session_arrivee_id: number;
    site_arrivee_id: number;
    room_arrivee_id: number;
    group_id: number;
    heure_depart: string;
    heure_arrivee: string;
    duree_trajet_minutes: number;
    created_at: string;
    updated_at: string;
    
    // Relations chargées
    group?: {
        id: number;
        name: string;
        size: number;
        main_site_id: number;
    };
    
    sessionDepart?: {
        id: number;
        start_at: string;
        end_at: string;
        course?: {
            id: number;
            code: string;
            title: string;
        };
        site?: {
            id: number;
            name: string;
        };
        room?: {
            id: number;
            name: string;
        };
    };
    
    sessionArrivee?: {
        id: number;
        start_at: string;
        end_at: string;
        course?: {
            id: number;
            code: string;
            title: string;
        };
        site?: {
            id: number;
            name: string;
        };
        room?: {
            id: number;
            name: string;
        };
    };
    
    siteDepart?: {
        id: number;
        name: string;
    };
    
    siteArrivee?: {
        id: number;
        name: string;
    };
    
    roomDepart?: {
        id: number;
        name: string;
    };
    
    roomArrivee?: {
        id: number;
        name: string;
    };
}
