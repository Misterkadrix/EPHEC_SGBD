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
          <select v-model="selectedUniversityId" @change="onUniversityChange" class="filter-select">
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
          <select v-model="selectedGroupId" @change="onGroupChange" class="filter-select">
            <option value="">Tous les groupes</option>
            <option v-for="group in filteredGroups" :key="group.id" :value="group.id">
              {{ group.name }} ({{ group.quantity }} étudiants)
            </option>
          </select>
        </div>

        <!-- Select Vue -->
        <div class="filter-item">
          <label class="filter-label">
            Vue
          </label>
          <select v-model="selectedView" @change="onViewChange" class="filter-select">
            <option value="month">Mois</option>
            <option value="week">Semaine</option>
            <option value="day">Jour</option>
          </select>
        </div>
      </div>
    </div>



    <!-- Grille du calendrier -->
    <div v-if="selectedView === 'month'" class="calendar-grid">
      <!-- Navigation du calendrier -->
      <div class="calendar-header">
        <button @click="previousMonth" class="nav-button">
          ←
        </button>

        <h2 class="calendar-title">
          {{ currentMonthName }} {{ currentYear }}
        </h2>

        <button @click="nextMonth" class="nav-button">
          →
        </button>
      </div>
      <!-- En-têtes des jours -->
      <div class="calendar-weekdays">
        <div v-for="day in weekDays" :key="day" class="calendar-weekday">
          {{ day }}
        </div>
      </div>

      <!-- Jours du mois -->
      <div class="calendar-days">
        <div v-for="day in calendarDays" :key="day.date" :class="[
          'calendar-day',
          day.isCurrentMonth ? 'current-month' : 'other-month',
          day.isToday ? 'today' : '',
          day.hasSessions ? 'has-sessions' : ''
        ]">
          <div class="day-number">{{ day.dayNumber }}</div>

          <!-- Sessions du jour -->
          <div v-if="day.sessions.length > 0" class="day-sessions">
            <div v-for="session in day.sessions.slice(0, 3)" :key="session.id" class="session-item"
              :title="`${session.course.title} - ${session.start_at} (${session.room.name})`">
              <div class="session-time">{{ session.start_at }}</div>
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

    <!-- Vue Semaine -->
    <div v-else-if="selectedView === 'week'" class="calendar-week">
      <!-- Navigation de la semaine -->
      <div class="calendar-header">
        <button @click="previousWeek" class="nav-button">
          ←
        </button>

        <h2 class="calendar-title">
          Semaine du {{ getWeekStartDate() }} au {{ getWeekEndDate() }}
        </h2>

        <button @click="nextWeek" class="nav-button">
          →
        </button>
      </div>

      <!-- En-têtes des jours de la semaine -->
      <div class="week-header">
        <div v-for="day in weekDays" :key="day" class="week-day-header">
          {{ day }}
        </div>
      </div>

      <!-- Grille de la semaine -->
      <div class="week-grid">
        <div v-for="day in weekDaysWithSessions" :key="day.date" class="week-day">
          <div class="week-day-title">{{ day.name }}</div>

          <!-- Sessions du jour -->
          <div v-if="day.sessions.length > 0" class="week-day-sessions">
            <div v-for="session in day.sessions" :key="session.id" class="week-session-item"
              :title="`${session.course.title} - ${session.start_at} (${session.room.name})`">
              <div class="week-session-time">{{ formatTime(session.start_at) }}</div>
              <div class="week-session-course">{{ session.course.title }}</div>
              <div class="week-session-room">{{ session.room.name }}</div>
            </div>
          </div>

          <div v-else class="week-no-sessions">
            Aucune session
          </div>
        </div>
      </div>
    </div>

    <!-- Vue Jour -->
    <div v-else-if="selectedView === 'day'" class="calendar-day-view">
      <!-- Navigation du jour -->
      <div class="calendar-header">
        <button @click="previousDay" class="nav-button">
          ←
        </button>

        <h2 class="calendar-title">
          {{ getCurrentDayDate() }}
        </h2>

        <button @click="nextDay" class="nav-button">
          →
        </button>
      </div>

      <!-- En-tête du jour -->
      <div class="day-header">
        <div class="day-title">
        </div>
      </div>

      <!-- Grille du jour -->
      <div class="day-grid">
        <div class="day-time-slots">
          <!-- Créneaux horaires de 8h à 18h -->
          <div v-for="hour in 11" :key="hour" class="time-slot">
            <div class="time-label">{{ 8 + hour - 1 }}:00</div>
            <div class="time-content">
              <!-- Ici on mettra plus tard les sessions pour cette heure -->
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
const selectedView = ref('month');

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

const onViewChange = () => {
  console.log('Vue sélectionnée:', selectedView.value);
};

const getDaySessions = (day: string) => {
  return filteredSessions.value.filter(session => {
    const sessionDate = new Date(session.start_at);
    const sessionDateString = sessionDate.toISOString().split('T')[0];
    return sessionDateString === day;
  });
};

const formatTime = (time: string) => {
  const date = new Date(time);
  return date.toLocaleTimeString('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getWeekDays = () => {
  const days = [];
  for (let i = 0; i < 7; i++) {
    days.push(weekDays[i]);
  }
  return days;
};

// Générer les jours de la semaine
const weekDaysWithSessions = computed(() => {
  const today = new Date();
  const currentDayOfWeek = today.getDay(); // 0 = Dimanche, 1 = Lundi, etc.

  // Calculer le lundi de la semaine actuelle
  const monday = new Date(today);
  const daysToMonday = currentDayOfWeek === 0 ? 6 : currentDayOfWeek - 1; // Lundi = 1
  monday.setDate(today.getDate() - daysToMonday);

  const weekDaysData: any[] = [];

  // Générer les 7 jours de la semaine (Lundi à Dimanche)
  for (let i = 0; i < 7; i++) {
    const currentDate = new Date(monday);
    currentDate.setDate(monday.getDate() + i);

    // Utiliser la même logique que createCalendarDay
    const dateString = currentDate.toISOString().split('T')[0];
    const isToday = currentDate.toDateString() === today.toDateString();

    // Trouver les sessions pour ce jour (même logique que dans createCalendarDay)
    const daySessions = filteredSessions.value.filter(session => {
      const sessionDate = new Date(session.start_at);
      const sessionDateString = sessionDate.toISOString().split('T')[0];
      return sessionDateString === dateString;
    });

    if (daySessions.length > 0) {
      console.log(`✅ Semaine - ${dateString}: ${daySessions.length} sessions`);
    }

    weekDaysData.push({
      name: weekDays[i],
      date: dateString,
      dayNumber: currentDate.getDate(),
      isToday,
      hasSessions: daySessions.length > 0,
      sessions: daySessions.map(session => ({
        ...session,
        start_time: new Date(session.start_at).toLocaleTimeString('fr-FR', {
          hour: '2-digit',
          minute: '2-digit'
        })
      }))
    });
  }

  return weekDaysData;
});

// Navigation pour la vue semaine
const previousWeek = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value, currentDate.value.getDate() - 7);
};

const nextWeek = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value, currentDate.value.getDate() + 7);
};

// Navigation pour la vue jour
const previousDay = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value, currentDate.value.getDate() - 1);
};

const nextDay = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value, currentDate.value.getDate() + 1);
};

// Helper pour la vue semaine
const getWeekStartDate = () => {
  const monday = new Date(currentDate.value);
  const daysToMonday = monday.getDay() === 0 ? 6 : monday.getDay() - 1; // Lundi = 1
  monday.setDate(monday.getDate() - daysToMonday);
  return monday.toLocaleDateString('fr-FR', { month: 'numeric', day: 'numeric' });
};

const getWeekEndDate = () => {
  const sunday = new Date(currentDate.value);
  const daysToSunday = sunday.getDay() === 0 ? 0 : 7 - sunday.getDay(); // Dimanche = 0
  sunday.setDate(sunday.getDate() + daysToSunday);
  return sunday.toLocaleDateString('fr-FR', { month: 'numeric', day: 'numeric' });
};

// Helper pour la vue jour
const getCurrentDayDate = () => {
  return currentDate.value.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
};


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

/* Styles pour les déplacements */
.deplacement-item {
  font-size: 12px;
  padding: 4px;
  border-radius: 4px;
  cursor: pointer;
  margin-bottom: 2px;
}

.deplacement-short {
  background-color: #fef3c7;
  color: #92400e;
  border-left: 3px solid #f59e0b;
}

.deplacement-long {
  background-color: #fee2e2;
  color: #991b1b;
  border-left: 3px solid #ef4444;
}

.deplacement-time {
  font-weight: 500;
  font-size: 11px;
}

.deplacement-info {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-size: 11px;
}

.deplacement-duration {
  font-size: 10px;
  opacity: 0.75;
}

.deplacement-item:hover {
  opacity: 0.8;
}

/* Styles pour le calendrier semaine */
.calendar-week {
  width: 100%;
}

.week-header {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 4px;
  margin-bottom: 16px;
}

.week-day-header {
  text-align: center;
  font-size: 14px;
  font-weight: 500;
  color: #6b7280;
  padding: 8px 0;
  background-color: #f3f4f6;
  border-radius: 6px;
}

.week-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 8px;
}

.week-day {
  min-height: 200px;
  padding: 12px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  background-color: white;
}

.week-day-title {
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 12px;
  text-align: center;
  color: #374151;
}

.week-day-sessions {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.week-session-item {
  font-size: 11px;
  padding: 6px;
  background-color: #dbeafe;
  border-radius: 4px;
  color: #1e40af;
  cursor: pointer;
}

.week-session-item:hover {
  background-color: #bfdbfe;
}

.week-session-time {
  font-weight: 500;
  font-size: 10px;
}

.week-session-course {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-size: 10px;
}

.week-session-room {
  font-size: 9px;
  opacity: 0.75;
}

.week-no-sessions {
  font-size: 12px;
  color: #9ca3af;
  text-align: center;
  font-style: italic;
  margin-top: 20px;
}

/* Styles pour la vue jour */
.calendar-day-view {
  width: 100%;
  padding: 20px;
  background-color: white;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.day-header {
  display: flex;
  justify-content: center;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 2px solid #e5e7eb;
}

.day-title {
  font-size: 22px;
  font-weight: 700;
  color: #111827;
  text-transform: capitalize;
}

.day-grid {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.day-time-slots {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.time-slot {
  display: flex;
  align-items: stretch;
  min-height: 80px;
  border-bottom: 1px solid #f3f4f6;
  position: relative;
}

.time-slot:last-child {
  border-bottom: none;
}

.time-label {
  width: 100px;
  padding: 12px 16px;
  background-color: #f9fafb;
  border-right: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  position: sticky;
  left: 0;
  z-index: 10;
}

.time-content {
  flex: 1;
  padding: 12px 16px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background-color: white;
  position: relative;
}

.time-content::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 1px;
  background-color: #f3f4f6;
}

/* Alternance des couleurs pour une meilleure lisibilité */
.time-slot:nth-child(even) .time-content {
  background-color: #fafafa;
}

.time-slot:nth-child(odd) .time-content {
  background-color: white;
}

/* Hover effect */
.time-slot:hover .time-content {
  background-color: #f0f9ff;
}

.time-slot:hover .time-label {
  background-color: #e0f2fe;
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
