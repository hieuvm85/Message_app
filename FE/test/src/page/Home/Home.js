
import React, { useEffect, useState } from 'react';
import axios from 'axios';
import Pusher from 'pusher-js';
import './Home.css'
import { Navigate,useNavigate  } from 'react-router-dom';
import { format } from 'date-fns';

function Home() {
  const [user, setUser] = useState(null);
  const [groups, setGroups] = useState([]);
  const [searchActive,setSearchActive]=useState(false);
  const [refresh,setRefresh] = useState(true);
  const navigate = useNavigate();
  const token = localStorage.getItem('token');
  

  useEffect(() => {
    const fetchUserData = async () => {
      // const token = localStorage.getItem('token');
      if (token) {
        try {
          const response = await axios.get('http://localhost:8000/api/user', {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }); 
          setUser(response.data);
         
          // Khởi tạo Pusher sau khi người dùng được tải
          const pusher = new Pusher('62f650b94e55b6d8db34', {
            cluster: 'ap1',
            // authEndpoint: 'http://localhost:8000/api/pusher/auth', // Địa chỉ xác thực
            // auth: {
            //   headers: {
            //     Authorization: `Bearer ${localStorage.getItem('token')}`,
            //     Origin: "*"
            //   }
            // }
          }, );
          // console.log(pusher);
          const channel = pusher.subscribe('get-all-group-' + response.data.id);
          channel.bind('get-all-groups', function(data) {
            setRefresh(prevRefresh => !prevRefresh);
            // console.log(1);
          });

          return () => {
            channel.unbind_all();
            channel.unsubscribe();
          };
        } catch (error) {
          console.log('catch');
          // navigate('/login');
        }
      }
    };

    fetchUserData();
  }, []); // Chỉ chạy một lần khi component mount


  useEffect(()=>{
    const fetchUserData = async () => {
      if (token ) {
        try{
          const response =await axios.get('http://localhost:8000/api/group', {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }); 
          setGroups(response.data.data);
          console.log(response.data.data[0].users[0].name);
          // console.log(refresh);
          // console.log("aaa");
        }
        catch(error){
          console.log("bug");
        }
      }
    }
    fetchUserData();
  },[refresh])
  return (
    <div> 
      { user ?
      <div className='body'>
          <div className='chat-container'>
            { !searchActive  ?
              <div>
                <div className='header-container'>
                    <div style={{position:'absolute', left:'15px', top:'50%',transform:'translate(0,-50%)',fontWeight:'700',fontSize:'20px' }}>{user.name}</div>
                    <div  className='btn-nav'>
                      <div style={{position:'absolute', left:'50%', top:'50%',transform:'translate(-50%,-50%)' }}>☰</ div >
                    </div>
                </div>
                <div className='body-container'>
                    <div className='search-container'> 
                      <div className='search' onClick={()=>setSearchActive(prev=>!prev)}>
                        <div style={{marginLeft:'20px', color:'#76787c'}}> ⚲  Tìm kiếm </div>
                      </div>
                    </div>
                    <div className='groups-container'>
                      {groups.map((group) => (
                        <div key={group.id} className='group' onClick={()=>navigate('/group/'+group.id)}>
                          <div style={{width:'60px',height:'60px', position:'absolute',top:'0', left:'10px' }}>
                            <img style={{width:'100%',height:'100%',borderRadius: '50%' }} src="https://inkythuatso.com/uploads/thumbnails/800/2023/03/9-anh-dai-dien-trang-inkythuatso-03-15-27-03.jpg" ></img>
                          </div >
                          <div style={{height:'60px', maxWidth:'300px', position:'absolute',left:'90px',top:'0', alignContent:'center'}}  >
                            <div style={{maxWidth:'260px', fontSize: '18px',overflow:'hidden',whiteSpace: 'nowrap',textOverflow:'ellipsis'}} className= {group.unread_messages ?'group_unread' :'group_read'} > {( !(!Array.isArray(group.users) && !group.users.length> 2)    ? group.name : (group.user[0].id==user.id?group.user[1].name:group.user[0].name ))}</div> 
                            <div style={{display:'flex', justifyContent:'left' }}>
                              <div style={{fontSize: '14px', maxWidth:'230px' , width :'230px',position:'relative' , overflow:'hidden',whiteSpace: 'nowrap',textOverflow:'ellipsis'}} className= {group.unread_messages ?'group_unread' :'group_read'} > {group.last_message.sender_id==user.id ? 'Bạn: ' : ""}  {group.last_message.content }</div> 
                              <div style={{fontSize: '14px', maxWidth:'50px' , width :'50px',position:'relative', marginLeft:'10px'}} className= {group.unread_messages ?'group_unread' :'group_read'}>{format(new Date(group.last_message.created_at), 'HH:mm')}</div>
                            </div>

                          </div >
                          
                        </div>
                      ))}
                    </div>
                </div>
              </div>
            :
            <div onClick={()=>setSearchActive(prev=>!prev)} className='btn'>Tim cai gi o day? quay lai</div>

            }
              
          </div>
      </div>
      :""}
    </div>
  );
}

export default Home;
