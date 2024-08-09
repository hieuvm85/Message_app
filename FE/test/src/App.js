import logo from './logo.svg';
import './App.css';

import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import Login from './page/Login/Login';
import Home from './page/Home/Home';
import PrivateRoute from './component/PrivateRoute';
import Group from './page/Group/Group';

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route path="/" element={<PrivateRoute component={Home} />} />
        <Route path="/group/:id" element={<PrivateRoute component={Group} />} />
      </Routes>
    </Router>
  );
}

export default App;
