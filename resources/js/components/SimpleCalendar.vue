<template>
  <div class="calendar-container">

    <!-- Filtres -->
    <div class="filters">
      <div class="filters-grid">
        <!-- Select Université -->
        <div class="filter-item">
          <label class="filter-label">
            Université
          </label>
          <select 
            v-model="selectedUniversityId" 
            @change="onUniversityChange"
            class="filter-select"
          >
            <option value="">Toutes les universités</option>
            <option v-for="univ in universities" :key="univ.id" :value="univ.id">
              {{ univ.name }} ({{ univ.code }})
            </option>
          </select>
        </div>

        <!-- Select Groupe -->
        <div class="filter-item">
          <label class="filter-label">
            Groupe
          </label>
          <select 
            v-model="selectedGroupId" 
            @change="onGroupChange"
            class="filter-select"
          >
            <option value="">Tous les groupes</option>
            <option v-for="group in filteredGroups" :key="group.id" :value="group.id">
              {{ group.name }} ({{ group.quantity }} étudiants)
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Navigation du calendrier -->
    <div class="calendar-header">
      <button 
        @click="previousMonth" 
        class="nav-button"
      >
        ←
      </button>
      
      <h2 class="calendar-title">
        {{ currentMonthName }} {{ currentYear }}
      </h2>
      
      <button 
        @click="nextMonth" 
        class="nav-button"
      >
        →
      </button>
    </div>

    <!-- Grille du calendrier -->
    <div class="calendar-grid">
      <!-- En-têtes des jours -->
      <div class="calendar-weekdays">
        <div v-for="day in weekDays" :key="day" class="calendar-weekday">
          {{ day }}
        </div>
      </div>

      <!-- Jours du mois -->
      <div class="calendar-days">
        <div 
          v-for="day in calendarDays" 
          :key="day.date" 
          :class="[
            'calendar-day',
            day.isCurrentMonth ? 'current-month' : 'other-month',
            day.isToday ? 'today' : '',
            day.hasSessions ? 'has-sessions' : ''
          ]"
        >
          <div class="day-number">{{ day.dayNumber }}</div>
          
          <!-- Sessions du jour -->
          <div v-if="day.sessions.length > 0" class="day-sessions">
            <div 
              v-for="session in day.sessions.slice(0, 3)" 
              :key="session.id"
              class="session-item"
              :title="`${session.course.title} - ${session.start_time} (${session.room.name})`"
            >
              <div class="session-time">{{ session.start_time }}</div>
              <div class="session-course">{{ session.course.title }}</div>
              <div class="session-room">{{ session.room.name }}</div>
            </div>
            
            <div v-if="day.sessions.length > 3" class="more-sessions">
              +{{ day.sessions.length - 3 }} autres
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';

interface University {
  id: number;
  name: string;
  code: string;
}

interface Group {
  id: number;
  name: string;
  quantity: number;
  university_id: number;
}

interface Course {
  id: number;
  title: string;
  code: string;
  university_id: number;
}

interface Room {
  id: number;
  name: string;
}

interface Session {
  id: number;
  start_at: string;
  end_at: string;
  course: Course;
  room: Room;
  groups: Group[];
}

interface CalendarDay {
  date: string;
  dayNumber: number;
  isCurrentMonth: boolean;
  isToday: boolean;
  hasSessions: boolean;
  sessions: Session[];
}

interface Props {
  universities: University[];
  groups: Group[];
  sessions: Session[];
}

const props = defineProps<Props>();

// État local
const currentDate = ref(new Date(2024, 8, 1)); // Septembre 2024
const selectedUniversityId = ref<string>('');
const selectedGroupId = ref<string>('');

// Jours de la semaine
const weekDays = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];

// Computed properties
const currentMonth = computed(() => currentDate.value.getMonth());
const currentYear = computed(() => currentDate.value.getFullYear());
const currentMonthName = computed(() => {
  const months = [
    'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
  ];
  return months[currentMonth.value];
});

// Filtrer les groupes par université
const filteredGroups = computed(() => {
  console.log('=== FILTRAGE DES GROUPES ===');
  console.log('Université sélectionnée:', selectedUniversityId.value);
  console.log('Tous les groupes:', props.groups.length);
  
  if (!selectedUniversityId.value || selectedUniversityId.value === '') {
    console.log('Aucune université sélectionnée - tous les groupes affichés');
    return props.groups;
  }
  
  const filtered = props.groups.filter(group => {
    const groupUnivId = String(group.university_id);
    const selectedUnivId = String(selectedUniversityId.value);
    const match = groupUnivId === selectedUnivId;
    console.log(`Groupe ${group.name}: university_id=${groupUnivId} vs selected=${selectedUnivId} => ${match ? '✅' : '❌'}`);
    return match;
  });
  
  console.log(`Groupes filtrés: ${filtered.length}/${props.groups.length}`);
  return filtered;
});

// Filtrer les sessions selon les sélections
const filteredSessions = computed(() => {
  let sessions = props.sessions;

  console.log('=== FILTRAGE SIMPLIFIÉ ===');
  console.log('Sessions originales:', sessions.length);
  console.log('Université sélectionnée:', selectedUniversityId.value, 'Type:', typeof selectedUniversityId.value);
  console.log('Groupe sélectionné:', selectedGroupId.value, 'Type:', typeof selectedGroupId.value);

  // Filtrer par université (seulement si une université est sélectionnée)
  if (selectedUniversityId.value && selectedUniversityId.value !== '') {
    const selectedUnivId = selectedUniversityId.value;
    console.log('Filtrage par université:', selectedUnivId, 'Type:', typeof selectedUnivId);
    sessions = sessions.filter(session => {
      console.log(`\n--- Session ${session.id} (${session.course.title}) ---`);
      console.log('Course university_id:', session.course.university_id, 'Type:', typeof session.course.university_id);
      console.log('Session groups:', session.groups);
      
      // Normaliser les types pour la comparaison
      const courseUnivId = String(session.course.university_id);
      const selectedUnivIdStr = String(selectedUnivId);
      
      // Vérifier l'université du cours
      const courseMatch = courseUnivId === selectedUnivIdStr;
      console.log('Course match:', courseMatch, `(${courseUnivId} === ${selectedUnivIdStr})`);
      
      // Vérifier les groupes
      const groupMatch = session.groups.some(group => {
        const groupUnivId = String(group.university_id);
        const match = groupUnivId === selectedUnivIdStr;
        console.log(`  Group: ${group.name} university_id: ${groupUnivId} vs ${selectedUnivIdStr} => ${match ? '✅' : '❌'}`);
        return match;
      });
      
      const keep = courseMatch || groupMatch;
      console.log(`Session ${session.id}: cours=${courseMatch}, groupe=${groupMatch} => ${keep ? '✅' : '❌'}`);
      
      return keep;
    });
  } else {
    console.log('Aucune université sélectionnée - toutes les sessions affichées');
  }

  // Filtrer par groupe (seulement si un groupe est sélectionné)
  if (selectedGroupId.value && selectedGroupId.value !== '') {
    const selectedGroupIdStr = selectedGroupId.value;
    console.log('Filtrage par groupe:', selectedGroupIdStr, 'Type:', typeof selectedGroupIdStr);
    sessions = sessions.filter(session => {
      console.log(`\n--- Filtrage groupe pour Session ${session.id} ---`);
      console.log('Session groups:', session.groups);
      
      const hasGroup = session.groups.some(group => {
        const groupId = String(group.id);
        const selectedId = String(selectedGroupIdStr);
        const match = groupId === selectedId;
        console.log(`  Groupe ${group.name}: ID ${groupId} === ${selectedId} => ${match ? '✅' : '❌'}`);
        return match;
      });
      
      console.log(`Session ${session.id} a le groupe ${selectedGroupIdStr}: ${hasGroup ? '✅' : '❌'}`);
      return hasGroup;
    });
  } else {
    console.log('Aucun groupe sélectionné - tous les groupes affichés');
  }

  console.log(`\n=== FIN FILTRAGE: ${sessions.length} sessions ===`);
  return sessions;
});

// Générer les jours du calendrier
const calendarDays = computed(() => {
  const year = currentYear.value;
  const month = currentMonth.value;
  
  // Premier jour du mois
  const firstDay = new Date(year, month, 1);
  // Dernier jour du mois
  const lastDay = new Date(year, month + 1, 0);
  
  // Premier jour de la semaine (Lundi = 1)
  const firstDayOfWeek = firstDay.getDay() === 0 ? 7 : firstDay.getDay();
  
  const days: CalendarDay[] = [];
  
  // Ajouter les jours du mois précédent
  for (let i = firstDayOfWeek - 1; i > 0; i--) {
    const date = new Date(year, month, 1 - i);
    days.push(createCalendarDay(date, false));
  }
  
  // Ajouter les jours du mois actuel
  for (let i = 1; i <= lastDay.getDate(); i++) {
    const date = new Date(year, month, i);
    days.push(createCalendarDay(date, true));
  }
  
  // Ajouter les jours du mois suivant pour compléter la grille
  const remainingDays = 42 - days.length; // 6 semaines * 7 jours
  for (let i = 1; i <= remainingDays; i++) {
    const date = new Date(year, month + 1, i);
    days.push(createCalendarDay(date, false));
  }
  
  return days;
});

// Créer un jour du calendrier
const createCalendarDay = (date: Date, isCurrentMonth: boolean): CalendarDay => {
  const dateString = date.toISOString().split('T')[0];
  const today = new Date();
  const isToday = date.toDateString() === today.toDateString();
  
  // Trouver les sessions pour ce jour
  const daySessions = filteredSessions.value.filter(session => {
    const sessionDate = new Date(session.start_at);
    const sessionDateString = sessionDate.toISOString().split('T')[0];
    return sessionDateString === dateString;
  });
  
  if (daySessions.length > 0) {
    console.log(`✅ ${dateString}: ${daySessions.length} sessions`);
  }
  
  return {
    date: dateString,
    dayNumber: date.getDate(),
    isCurrentMonth,
    isToday,
    hasSessions: daySessions.length > 0,
    sessions: daySessions.map(session => ({
      ...session,
      start_time: new Date(session.start_at).toLocaleTimeString('fr-FR', { 
        hour: '2-digit', 
        minute: '2-digit' 
      })
    }))
  };
};

// Navigation
const previousMonth = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value - 1, 1);
};

const nextMonth = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value + 1, 1);
};

// Gestion des changements de filtres
const onUniversityChange = () => {
  selectedGroupId.value = '';
};

const onGroupChange = () => {
  // Pas besoin de faire quoi que ce soit de spécial
};

// Initialisation
onMounted(() => {
  // Sélectionner la première université par défaut si disponible
  if (props.universities.length > 0 && !selectedUniversityId.value) {
    selectedUniversityId.value = props.universities[0].id.toString();
  }
});
</script>

<style scoped>
.calendar-container {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  padding: 24px;
}

.debug-info {
  background-color: #f3f4f6;
  padding: 16px;
  border-radius: 6px;
  margin-bottom: 16px;
}

.debug-info h3 {
  font-weight: bold;
  margin-bottom: 8px;
}

.debug-info p {
  margin-bottom: 4px;
}

.filters {
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 16px;
  margin-bottom: 16px;
}

.filters-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.filter-item {
  display: flex;
  flex-direction: column;
}

.filter-label {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 8px;
}

.filter-select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
}

.filter-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.calendar-header {
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 16px;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.calendar-title {
  font-size: 20px;
  font-weight: 600;
  color: #111827;
}

.nav-button {
  padding: 8px;
  border-radius: 6px;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 18px;
  color: #6b7280;
}

.nav-button:hover {
  background-color: #f3f4f6;
}

.calendar-grid {
  width: 100%;
}

.calendar-weekdays {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 4px;
  margin-bottom: 8px;
}

.calendar-weekday {
  text-align: center;
  font-size: 14px;
  font-weight: 500;
  color: #6b7280;
  padding: 8px 0;
}

.calendar-days {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 4px;
}

.calendar-day {
  min-height: 120px;
  padding: 8px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  position: relative;
}

.calendar-day.other-month {
  background-color: #f9fafb;
  color: #9ca3af;
}

.calendar-day.current-month {
  background-color: white;
  color: #111827;
}

.calendar-day.today {
  background-color: #eff6ff;
  border-color: #93c5fd;
}

.calendar-day.has-sessions {
  border-color: #34d399;
}

.day-number {
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 8px;
}

.day-sessions {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.session-item {
  font-size: 12px;
  padding: 4px;
  background-color: #dbeafe;
  border-radius: 4px;
  color: #1e40af;
  cursor: pointer;
}

.session-item:hover {
  background-color: #bfdbfe;
}

.session-time {
  font-weight: 500;
}

.session-course {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.session-room {
  font-size: 12px;
  opacity: 0.75;
}

.more-sessions {
  font-size: 12px;
  color: #6b7280;
  text-align: center;
  font-style: italic;
}

/* Responsive */
@media (max-width: 768px) {
  .filters-grid {
    grid-template-columns: 1fr;
  }
  
  .calendar-day {
    min-height: 80px;
    padding: 4px;
  }
  
  .session-item {
    font-size: 12px;
    padding: 4px;
  }
  
  .session-course,
  .session-room {
    display: none;
  }
}
</style>
