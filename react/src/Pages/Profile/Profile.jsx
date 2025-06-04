import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

import './Profile.css';

import logo from './assets/images/logoBrand.png';
import bellIcon from './assets/images/bell.png';
import defaultPetImg from './assets/images/kucing-dan-anjing.webp';

const Profile = () => {
  const [user, setUser] = useState(null);
  const [pets, setPets] = useState([]);
  const [newPet, setNewPet] = useState({ name: '', type: 'Kucing', gender: 'Jantan', age: '' });
  const [isLoading, setIsLoading] = useState(true);
  const navigate = useNavigate();

  const token = localStorage.getItem('token');

  useEffect(() => {
    if (!token) {
      navigate('/login');
      return;
    }

    fetch('http://localhost:5051/api/me', {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then(res => {
        if (!res.ok) throw new Error('Invalid token');
        return res.json();
      })
      .then(data => {
        setUser(data);
        return fetch(`http://localhost:5051/api/pat/${data.email}`);
      })
      .then(res => res.json())
      .then(petList => {
        setPets(petList);
        setIsLoading(false);
      })
      .catch(() => {
        localStorage.removeItem('token');
        navigate('/login');
      });
  }, [navigate, token]);

  const handleAddPet = (e) => {
    e.preventDefault();
    if (!newPet.name || !newPet.age) return;

    const payload = { ...newPet, owner: user.email };

    fetch('http://localhost:5051/api/pat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`
      },
      body: JSON.stringify(payload),
    })
      .then(res => res.json())
      .then(saved => {
        setPets([...pets, saved]);
        setNewPet({ name: '', type: 'Kucing', gender: 'Jantan', age: '' });
        navigate(0);
      })
      .catch(console.error);
  };

  if (isLoading) return <p>Loading...</p>;

  return (
    <div className="dashboard-container">
      <header className="top-header">
        <div className="header-left">
          <img src={logo} alt="Logo" className="logo" />
          <span className="logo-text">PurrfectMatch</span>
        </div>
        <div className="header-right">
          <div className="notification-icon">
            <img src={bellIcon} alt="bell" className="bell-icon" />
            <span className="notification-badge">3</span>
          </div>
          {/* <div className="avatar"> */}
          <div 
            className="avatar"
            style={{ cursor: 'pointer' }}
            onClick={() => navigate('/profile')}
            title="Go to Profile"
          >
            <span className="avatar-initial">{user.name?.[0]?.toUpperCase()}</span>
            <span className="online-dot"></span>
          </div>
        </div>
      </header>

      <div className="main-layout">
        <aside className="sidebar">
          <nav className="nav">
            <div className="nav-item active-tab" onClick={() => navigate('/dashboard')}>
              <span>Kembali ke Dashboard</span>
            </div>
          </nav>
        </aside>

        <main className="main-content">
          <h1 className="tab-title">Profil Pengguna</h1>
          <div className="profile-info">
            <p><strong>Nama:</strong> {user.name}</p>
            <p><strong>Email:</strong> {user.email}</p>
          </div>

          <h2 className="tab-title mt-4">Hewan Peliharaan Saya</h2>
          <div className="stats-section">
            {pets.length === 0 ? (
              <p>Kamu belum menambahkan hewan peliharaan.</p>
            ) : (
              pets.map((pet, index) => (
                <div key={index} className="stat-card">
                  <div className="stat-header">
                    <img src={defaultPetImg} alt="pet" className="stat-icon" />
                    {/* <h3>{pet.name}</h3> */}
                  </div>
                  <div className="stat-main-value">
                    <p><strong>Nama:</strong> {pet.name}</p>
                    <p><strong>Jenis:</strong> {pet.type}</p>
                    <p><strong>Gender:</strong> {pet.gender}</p>
                    <p><strong>Umur:</strong> {pet.age} tahun</p>
                  </div>
                </div>
              ))
            )}
          </div>

          <h2 className="tab-title mt-4">Tambah Hewan Baru</h2>
          <form className="pet-form" onSubmit={handleAddPet}>
            <div className="form-group">
              <label>Nama</label>
              <input
                type="text"
                value={newPet.name}
                onChange={(e) => setNewPet({ ...newPet, name: e.target.value })}
                required
              />
            </div>
            <div className="form-group">
              <label>Jenis</label>
              <select
                value={newPet.type}
                onChange={(e) => setNewPet({ ...newPet, type: e.target.value })}
              >
                <option value="Kucing">Kucing</option>
                <option value="Anjing">Anjing</option>
              </select>
            </div>
            <div className="form-group">
              <label>Gender</label>
              <select
                value={newPet.gender}
                onChange={(e) => setNewPet({ ...newPet, gender: e.target.value })}
              >
                <option value="Jantan">Jantan</option>
                <option value="Betina">Betina</option>
              </select>
            </div>
            <div className="form-group">
              <label>Umur (tahun)</label>
              <input
                type="number"
                value={newPet.age}
                onChange={(e) => setNewPet({ ...newPet, age: e.target.value })}
                required
              />
            </div>
            <button type="submit" className="submit-button">Tambah Hewan</button>
          </form>
        </main>
      </div>
    </div>
  );
};

export default Profile;